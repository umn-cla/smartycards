<?php

namespace App\Services\Lti;

use App\Enums\LtiActivityProgress;
use App\Enums\LtiGradingProgress;
use App\Jobs\SubmitLtiGrade;
use App\Models\LtiGradeSubmission;
use App\Models\LtiPlatform;
use App\Models\LtiResourceLink;
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
     * Create a grade submission record and queue a job to submit it to Canvas
     * using Assignment and Grade Services (AGS)
     *
     * This method does NOT submit the grade synchronously - it queues a background job.
     * The actual submission happens asynchronously with automatic retries.
     */
    public function queueGradeSubmissionFromLaunchId(
        string $launchId,
        int $userId,
        ?int $activityEventId = null,
        float $scoreGiven = 100.0,
        float $scoreMaximum = 100.0
    ): ?LtiGradeSubmission {
        // Get the launch from cache to extract required data
        $launch = $this->getLaunchFromCache($launchId);
        $launchData = $launch->getLaunchData();

        // Don't create grade submissions for staff (instructors/TAs)
        if ($this->hasStaffRole($launch)) {
            return null;
        }

        // Get the LTI user ID (sub claim)
        $ltiUserId = $launchData['sub'] ?? null;
        if (!$ltiUserId) {
            throw new \Exception('LTI user ID not found in launch data');
        }

        // Find the resource link
        $deploymentId = $launchData[LtiConstants::DEPLOYMENT_ID] ?? null;
        $issuer = $launchData['iss'] ?? null;
        $resourceLinkClaim = $launchData[LtiConstants::RESOURCE_LINK] ?? [];
        $resourceLinkId = $resourceLinkClaim['id'] ?? null;

        if (!$deploymentId || !$issuer || !$resourceLinkId) {
            throw new \Exception('Missing required LTI claims for grade submission');
        }

        $platform = LtiPlatform::where('issuer', $issuer)->firstOrFail();

        $deployment = $platform->deployments()
            ->where('deployment_id', $deploymentId)
            ->firstOrFail();

        $resourceLink = LtiResourceLink::where('lti_deployment_id', $deployment->id)
            ->where('resource_link_id', $resourceLinkId)
            ->firstOrFail();

        // Build request payload for audit trail
        $requestPayload = [
            'scoreGiven' => $scoreGiven,
            'scoreMaximum' => $scoreMaximum,
            'userId' => $ltiUserId,
            'timestamp' => date('c'),
            'activityProgress' => LtiActivityProgress::Completed->value,
            'gradingProgress' => LtiGradingProgress::FullyGraded->value,
        ];

        // Create the grade submission record
        $submission = LtiGradeSubmission::create([
            'lti_resource_link_id' => $resourceLink->id,
            'user_id' => $userId,
            'activity_event_id' => $activityEventId,
            'score_given' => $scoreGiven,
            'score_maximum' => $scoreMaximum,
            'activity_progress' => LtiActivityProgress::Completed,
            'grading_progress' => LtiGradingProgress::FullyGraded,
            'lti_user_id' => $ltiUserId,
            'launch_id' => $launchId,
            'submitted_at' => now(),
            'success' => false, // Will be updated by the job
            'request_payload' => $requestPayload,
        ]);

        // Dispatch the job to submit the grade asynchronously
        SubmitLtiGrade::dispatch($submission);

        return $submission;
    }

    /**
     * Queue a grade submission using membership data (no cache dependency)
     * Used when a user completes an assignment outside of Canvas (deferred completion)
     */
    public function queueGradeSubmissionFromMembership(
        \App\Models\LtiResourceLinkMembership $membership,
        ?int $activityEventId = null,
        float $scoreGiven = 100.0,
        float $scoreMaximum = 100.0
    ): ?LtiGradeSubmission {
        $resourceLink = $membership->resourceLink;

        if (!$resourceLink) {
            throw new \Exception('Resource link not found for membership');
        }

        if (!$membership->lti_user_id) {
            throw new \Exception('LTI user ID not found in membership (legacy data)');
        }

        if (!$resourceLink->lineitem_url) {
            throw new \Exception('Lineitem URL not available for this resource link');
        }

        $requestPayload = [
            'scoreGiven' => $scoreGiven,
            'scoreMaximum' => $scoreMaximum,
            'userId' => $membership->lti_user_id,
            'timestamp' => date('c'),
            'activityProgress' => LtiActivityProgress::Completed->value,
            'gradingProgress' => LtiGradingProgress::FullyGraded->value,
        ];

        $submission = LtiGradeSubmission::create([
            'lti_resource_link_id' => $resourceLink->id,
            'user_id' => $membership->user_id,
            'activity_event_id' => $activityEventId,
            'score_given' => $scoreGiven,
            'score_maximum' => $scoreMaximum,
            'activity_progress' => LtiActivityProgress::Completed,
            'grading_progress' => LtiGradingProgress::FullyGraded,
            'lti_user_id' => $membership->lti_user_id,
            'launch_id' => 'deferred',
            'submitted_at' => now(),
            'success' => false,
            'request_payload' => $requestPayload,
        ]);

        SubmitLtiGrade::dispatch($submission);

        return $submission;
    }

    /**
     * Submit a grade using an existing grade submission record
     * Requires the launch to still be cached (1hr TTL)
     */
    public function submitGradeFromSubmission(LtiGradeSubmission $submission)
    {
        // Verify we have required data
        if (!$submission->resourceLink) {
            throw new \Exception('Resource link not found for grade submission');
        }

        if (!$submission->resourceLink->lineitem_url) {
            throw new \Exception('Lineitem URL not available for this resource link');
        }

        // Try to get the launch from cache
        // Note: This depends on the launch still being cached
        // If launch expires, this will throw an exception and the job will retry
        try {
            $launch = $this->getLaunchFromCache($submission->launch_id);
        } catch (\Exception $e) {
            throw new \Exception("Launch not found in cache (may have expired): {$e->getMessage()}");
        }

        // Verify AGS is available
        if (!$launch->hasAgs()) {
            throw new \Exception('AGS service not available for this launch');
        }

        // Prepare the grade object
        $grade = LtiGrade::new()
            ->setScoreGiven($submission->score_given)
            ->setScoreMaximum($submission->score_maximum)
            ->setUserId($submission->lti_user_id)
            ->setTimestamp(date('c'))
            ->setActivityProgress($submission->activity_progress)
            ->setGradingProgress($submission->grading_progress);

        // Submit the grade
        $ags = $launch->getAgs();

        return $ags->putGrade($grade);
    }

    /**
     * Submit a grade without using cached launch data
     * Manually constructs OAuth token request and AGS submission
     * Used for deferred grade submissions
     */
    public function submitGradeFromMembershipData(LtiGradeSubmission $submission): array
    {
        $resourceLink = $submission->resourceLink()->with('deployment.platform')->first();

        if (!$resourceLink) {
            throw new \Exception('Resource link not found for submission');
        }

        if (!$resourceLink->lineitem_url) {
            throw new \Exception('Lineitem URL not available');
        }

        $deployment = $resourceLink->deployment;
        $platform = $deployment->platform;

        if (!$platform->auth_token_url) {
            throw new \Exception('Platform auth token URL not configured');
        }

        if (!$deployment->client_id) {
            throw new \Exception('Deployment client ID not configured');
        }

        $accessToken = $this->getAccessToken(
            $platform->auth_token_url,
            $deployment->client_id,
            $resourceLink->ags_scopes ?? []
        );

        $gradeData = [
            'userId' => $submission->lti_user_id,
            'scoreGiven' => $submission->score_given,
            'scoreMaximum' => $submission->score_maximum,
            'timestamp' => date('c'),
            'activityProgress' => $submission->activity_progress->value,
            'gradingProgress' => $submission->grading_progress->value,
        ];

        $response = $this->connector->makeServiceRequest(
            $resourceLink->ags_scopes ?? [],
            $resourceLink->lineitem_url.'/scores',
            false,
            'POST',
            $gradeData,
            $accessToken,
            'application/vnd.ims.lis.v1.score+json'
        );

        return $response;
    }

    /**
     * Get an OAuth access token for LTI service requests
     */
    private function getAccessToken(string $authTokenUrl, string $clientId, array $scopes): string
    {
        $scopeString = implode(' ', $scopes);

        $response = $this->connector->makeRequest([
            'grant_type' => 'client_credentials',
            'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            'client_assertion' => $this->generateClientAssertion($authTokenUrl, $clientId),
            'scope' => $scopeString,
        ], $authTokenUrl);

        $responseData = json_decode($response['body'], true);

        if (!isset($responseData['access_token'])) {
            throw new \Exception('Failed to obtain access token from platform');
        }

        return $responseData['access_token'];
    }

    /**
     * Generate a JWT client assertion for OAuth token requests
     */
    private function generateClientAssertion(string $authTokenUrl, string $clientId): string
    {
        $registration = $this->database->findRegistrationByIssuer($clientId);

        if (!$registration) {
            throw new \Exception("Registration not found for client ID: {$clientId}");
        }

        $messageJwt = [
            'iss' => $clientId,
            'sub' => $clientId,
            'aud' => $authTokenUrl,
            'iat' => time(),
            'exp' => time() + 60,
            'jti' => 'lti-service-token-'.uniqid(),
        ];

        return $registration->signJwt($messageJwt);
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
     * Create or update LTI resource link membership for a user
     * Tracks which users have what roles in which Canvas courses
     */
    public function createOrUpdateMembership(
        LtiMessageLaunch $launch,
        User $user,
        \App\Models\LtiResourceLink $resourceLink
    ): \App\Models\LtiResourceLinkMembership {
        $launchData = $launch->getLaunchData();
        $roles = $launchData[LtiConstants::ROLES] ?? [];
        $isStaff = $this->hasStaffRole($launch);
        $ltiUserId = $launchData['sub'] ?? null;

        return \App\Models\LtiResourceLinkMembership::updateOrCreate(
            [
                'user_id' => $user->id,
                'lti_resource_link_id' => $resourceLink->id,
            ],
            [
                'roles' => $roles,
                'is_staff' => $isStaff,
                'last_launch_at' => now(),
                'lti_user_id' => $ltiUserId,
            ]
        );
    }
}
