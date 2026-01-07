<?php

namespace App\Services\Lti;

use App\Enums\LtiActivityProgress;
use App\Enums\LtiGradingProgress;
use App\Jobs\SubmitLtiScore;
use App\Models\LtiPlatform;
use App\Models\LtiResourceLinkEntry;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Packback\Lti1p3\DeepLinkResources\Resource;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\JwksEndpoint;
use Packback\Lti1p3\LtiAssignmentsGradesService;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\LtiGrade;
use Packback\Lti1p3\LtiMessageLaunch;
use Packback\Lti1p3\LtiOidcLogin;

class LtiService
{
    public function __construct(
        private IDatabase $database,
        private ICache $cache,
        private ICookie $cookie,
        private ILtiServiceConnector $connector
    ) {}

    /**
     * Handle OIDC login request from LMS
     */
    public function login(array $request, string $launchUrl)
    {
        $login = new LtiOidcLogin(
            $this->database,
            $this->cache,
            $this->cookie,
        );

        $redirectUrl = $login->getRedirectUrl($launchUrl, $request);

        return redirect($redirectUrl);
    }

    /**
     * Handle and validate the LTI message launch
     */
    public function validateAndCacheLaunch(array $request)
    {
        $launch = LtiMessageLaunch::new(
            $this->database,
            $this->cache,
            $this->cookie,
            $this->connector,
        );

        return $launch->initialize($request);
    }

    /**
     * Retrieve a previously validated launch
     */
    public function getLaunchFromCache(string $launchId)
    {
        return LtiMessageLaunch::fromCache(
            $launchId,
            $this->database,
            $this->cache,
            $this->cookie,
            $this->connector,
        );
    }

    /**
     * Check if the user has a staff role (instructor, TA, admin, etc.)
     */
    public function hasStaffRole(LtiMessageLaunch $launch): bool
    {
        $staffRoles = [
            LtiConstants::MEMBERSHIP_INSTRUCTOR,
            LtiConstants::MEMBERSHIP_TA,
            LtiConstants::MEMBERSHIP_CONTENTDEVELOPER,
        ];

        $launchData = $launch->getLaunchData();
        $userRoles = $launchData[LtiConstants::ROLES] ?? [];

        return !empty(array_intersect($userRoles, $staffRoles));
    }

    /**
     * Generate Deep Linking response
     */
    public function createDeepLinkResponse(string $launchId, array $requestData = [])
    {
        $launch = $this->getLaunchFromCache($launchId);

        if (!$launch->isDeepLinkLaunch()) {
            throw new LtiException('Not a deep linking launch');
        }

        $deeplink = $launch->getDeepLink();

        // Extract deck selection data from request
        $deckId = $requestData['deck_id'] ?? null;

        if (!$deckId) {
            throw new \InvalidArgumentException('Deck ID is required for deep link response');
        }

        // Create the resource that will be inserted into the LMS
        $resource = Resource::new()
            ->setUrl(route('lti.launch'))
            ->setCustomParams([
                // setting the deck as a custom param should let us
                // link back to the deck when course is cloned
                'deck_id' => $deckId,
            ]);

        // Get JWT for the response
        $jwt = $deeplink->getResponseJwt([$resource]);
        $returnUrl = $deeplink->returnUrl();

        // return the necessary data to create an auto-posting form
        return [
            'jwt' => $jwt,
            'return_url' => $returnUrl,
        ];
    }

    /**
     * Provide a JWKS endpoint for platforms to verify our signatures
     */
    public function getPublicJwks()
    {
        $privateKey = config('lti.private_key');
        $kid = config('lti.kid');

        if (!$privateKey || !$kid) {
            throw new \RuntimeException('LTI private key and KID must be configured');
        }

        return JwksEndpoint::new([$kid => $privateKey])
            ->getPublicJwks();
    }

    /**
     * Retrieve members via NRPS service
     * (Name Role Provisioning Service)
     */
    public function getMembers(LtiMessageLaunch $launch)
    {
        if (!$launch->hasNrps()) {
            return []; // Service not available
        }

        $nrps = $launch->getNrps();

        return $nrps->getMembers();
    }

    /**
     * Get all entries for a user and deck
     *
     * @return \Illuminate\Support\Collection Collection of LtiResourceLinkEntry objects
     */
    public function getEntriesForUserAndDeck(int $userId, int $deckId)
    {
        return LtiResourceLinkEntry::query()
            ->whereHas('resourceLink', function ($query) use ($deckId) {
                $query->where('deck_id', $deckId);
            })
            ->where('user_id', $userId)
            ->with('resourceLink.deployment.platform')
            ->get();
    }

    /**
     * Queue a score submission for an entry
     * Updates the score and queues it for submission to Canvas
     */
    public function queueScoreSubmission(
        LtiResourceLinkEntry $entry,
        int $userId,
        ?int $activityEventId = null,
        float $score = 100.0,
        float $scoreMaximum = 100.0
    ): LtiResourceLinkEntry {
        if (!$entry->lti_user_id) {
            throw new \Exception('LTI user ID not found in entry (legacy data)');
        }

        $resourceLink = $entry->resourceLink;

        if (!$resourceLink->lineitem_url) {
            throw new \Exception('Lineitem URL not available for this resource link');
        }

        // Update the entry with completion data
        $entry->update([
            'score' => $score,
            'score_maximum' => $scoreMaximum,
            'activity_event_id' => $activityEventId,
            'completed_at' => now(),
        ]);

        // Dispatch job to submit score to Canvas
        SubmitLtiScore::dispatch($entry);

        return $entry;
    }

    /**
     * Submit a score to Canvas using database-stored LTI configuration
     * Uses the Packback library's AGS service which handles OAuth tokens automatically
     */
    public function submitScore(LtiResourceLinkEntry $entry): array
    {
        $resourceLink = $entry->resourceLink()->with('deployment.platform')->first();

        if (!$resourceLink) {
            throw new \Exception('Resource link not found for entry');
        }

        if (!$resourceLink->lineitem_url) {
            throw new \Exception('Lineitem URL not available');
        }

        $deployment = $resourceLink->deployment;
        $platform = $deployment->platform;

        // Get the LTI registration from the database
        $registration = $this->database->findRegistrationByIssuer($platform->issuer, $deployment->client_id);

        if (!$registration) {
            throw new \Exception('LTI registration not found for platform');
        }

        // Build the AGS service data with endpoints and scopes
        $serviceData = [
            'scope' => $resourceLink->ags_scopes ?? [],
            'lineitem' => $resourceLink->lineitem_url,
        ];

        if ($resourceLink->lineitems_url) {
            $serviceData['lineitems'] = $resourceLink->lineitems_url;
        }

        // Create AGS service instance - it handles OAuth tokens automatically
        $ags = new LtiAssignmentsGradesService(
            $this->connector,
            $registration,
            $serviceData
        );

        // Prepare the grade object
        $grade = LtiGrade::new()
            ->setScoreGiven($entry->score)
            ->setScoreMaximum($entry->score_maximum)
            ->setUserId($entry->lti_user_id)
            ->setTimestamp(date('c'))
            ->setActivityProgress(LtiActivityProgress::Completed->value)
            ->setGradingProgress(LtiGradingProgress::FullyGraded->value);

        // Submit the score - library handles OAuth token acquisition
        return $ags->putGrade($grade);
    }

    /**
     * Retrieve groups via Groups service
     */
    public function getGroups(LtiMessageLaunch $launch)
    {
        if (!$launch->hasGs()) {
            return []; // Service not available
        }

        $gs = $launch->getGs();

        return $gs->getGroups();
    }

    /**
     * Retrieve groups by set via Groups service
     */
    public function getGroupsBySet(LtiMessageLaunch $launch)
    {
        if (!$launch->hasGs()) {
            return []; // Service not available
        }

        $gs = $launch->getGs();

        return $gs->getGroupsBySet();
    }

    /**
     * Validates that LTI Launch data contains everything
     * we expect to create or authenticate a user
     *
     * @return array - validated data
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateLtiLaunchData(array $launchData): array
    {
        // Basic validation rules for LTI launch
        $rules = [
            'sub' => 'required|string',
            'email' => 'required|email',
            // sis id
            // Escape the dots in the URL with backslashes, then use dot notation for nesting
            'https://purl\.imsglobal\.org/spec/lti/claim/lis.person_sourcedid' => 'required|string',
            'given_name' => 'sometimes|string',
            'family_name' => 'sometimes|string',
        ];

        $validated = Validator::make($launchData, $rules)->validate();

        // Normalize test SIS IDs in non-production environments
        if (!app()->isProduction()) {
            $validated[LtiConstants::LIS]['person_sourcedid'] = $this->normalizeDevSisId(
                $validated[LtiConstants::LIS]['person_sourcedid']
            );
        }

        return $validated;
    }

    /**
     * Maps test placeholders in Canvas dev instance to real emplids
     * Actual emplid values are set in .env file
     */
    private function normalizeDevSisId(string $sisId): string
    {
        $mappings = config('lti.dev_sis_mappings', []);

        return $mappings[$sisId] ?? $sisId;
    }

    /**
     * Create or update LTI resource link from launch data
     * Caches AGS endpoints and context information
     */
    public function createOrUpdateResourceLink(LtiMessageLaunch $launch, ?int $deckId = null): \App\Models\LtiResourceLink
    {
        $launchData = $launch->getLaunchData();

        // Get deployment info
        $deploymentId = $launchData[LtiConstants::DEPLOYMENT_ID] ?? null;
        $issuer = $launchData['iss'] ?? null;

        if (!$deploymentId || !$issuer) {
            throw new LtiException('Launch missing deployment ID or issuer');
        }

        // Find the deployment
        $platform = LtiPlatform::where('issuer', $issuer)->firstOrFail();

        $deployment = $platform->deployments()
            ->where('deployment_id', $deploymentId)
            ->firstOrFail();

        // Get resource link ID
        $resourceLinkClaim = $launchData[LtiConstants::RESOURCE_LINK] ?? [];
        $resourceLinkId = $resourceLinkClaim['id'] ?? null;

        if (!$resourceLinkId) {
            throw new LtiException('Launch missing resource link ID');
        }

        // Get context (course) information
        $contextClaim = $launchData[LtiConstants::CONTEXT] ?? [];
        $contextId = $contextClaim['id'] ?? null;
        $contextLabel = $contextClaim['label'] ?? null;
        $contextTitle = $contextClaim['title'] ?? null;

        // Get AGS endpoints if available
        $agsClaim = $launchData[LtiConstants::AGS_CLAIM_ENDPOINT] ?? [];
        $lineitemUrl = $agsClaim['lineitem'] ?? null;
        $lineitemsUrl = $agsClaim['lineitems'] ?? null;
        $agsScopes = $agsClaim['scope'] ?? [];

        // Get resource link title and description
        $resourceTitle = $resourceLinkClaim['title'] ?? null;
        $resourceDescription = $resourceLinkClaim['description'] ?? null;

        // Get custom params
        $customParams = $launchData[LtiConstants::CUSTOM] ?? [];

        // Create or update the resource link
        $resourceLink = \App\Models\LtiResourceLink::updateOrCreate(
            [
                'lti_deployment_id' => $deployment->id,
                'resource_link_id' => $resourceLinkId,
            ],
            [
                'title' => $resourceTitle,
                'description' => $resourceDescription,
                'context_id' => $contextId,
                'context_label' => $contextLabel,
                'context_title' => $contextTitle,
                'deck_id' => $deckId,
                'custom_params' => $customParams,
                'lineitem_url' => $lineitemUrl,
                'lineitems_url' => $lineitemsUrl,
                'ags_scopes' => $agsScopes,
            ]
        );

        return $resourceLink;
    }

    /**
     * Authenticate a user from an LTI launch
     * Creates a new user if one doesn't exist
     */
    public function authenticateFromLaunch(LtiMessageLaunch $launch): User
    {
        $launchData = $launch->getLaunchData();
        $validated = $this->validateLtiLaunchData($launchData);

        $ltiUserId = $validated['sub'];  // Canvas internal user ID
        $studentSisId = $validated[LtiConstants::LIS]['person_sourcedid'];  // emplid
        $email = $validated['email'];
        $firstName = $validated['given_name'] ?? '';
        $lastName = $validated['family_name'] ?? '';

        // Try finding by LTI user ID first (returning user)
        $user = User::where('lti_sub_id', $ltiUserId)->first();

        // Then by emplid (user exists but hasn't used LTI before)
        if (!$user) {
            $user = User::where('emplid', $studentSisId)->first();
        }

        // Create new user if not found
        if (!$user) {
            $user = User::create([
                'lti_sub_id' => $ltiUserId,
                'emplid' => $studentSisId,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'name' => trim($firstName.' '.$lastName),
                'password' => Hash::make(Str::random(32)),
            ]);

            Auth::login($user);

            return $user;
        }

        // Update existing user with any missing fields
        $user->update([
            'email' => $user->email ?? $email,
            'first_name' => $user->first_name ?? $firstName,
            'last_name' => $user->last_name ?? $lastName,
            'emplid' => $user->emplid ?? $studentSisId,
            'lti_sub_id' => $user->lti_sub_id ?? $ltiUserId,
        ]);

        Auth::login($user);

        return $user;
    }

    /**
     * Create or update LTI resource link entry for a user
     * Tracks which users have what roles in which Canvas courses
     */
    public function createOrUpdateEntry(
        LtiMessageLaunch $launch,
        User $user,
        \App\Models\LtiResourceLink $resourceLink
    ): LtiResourceLinkEntry {
        $launchData = $launch->getLaunchData();
        $roles = $launchData[LtiConstants::ROLES] ?? [];
        $isStaff = $this->hasStaffRole($launch);
        $ltiUserId = $launchData['sub'] ?? null;

        return LtiResourceLinkEntry::updateOrCreate(
            [
                'user_id' => $user->id,
                'lti_resource_link_id' => $resourceLink->id,
            ],
            [
                'lti_user_id' => $ltiUserId,
                'roles' => $roles,
                'is_staff' => $isStaff,
                'last_launch_at' => now(),
                'score_maximum' => 100.00,
            ]
        );
    }
}
