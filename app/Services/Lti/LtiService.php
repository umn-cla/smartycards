<?php

namespace App\Services\Lti;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Packback\Lti1p3\DeepLinkResources\Resource;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\JwksEndpoint;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\LtiGrade;
use Packback\Lti1p3\LtiMessageLaunch;
use Packback\Lti1p3\LtiOidcLogin;
use Packback\Lti1p3\LtiConstants;

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

        try {
            $redirectUrl = $login->getRedirectUrl($launchUrl, $request);
            return redirect($redirectUrl);
        } catch (LtiException $e) {
            // something?
            throw $e;
        }
    }

    /**
     * Handle and validate the LTI message launch
     */
    public function validateLaunch(array $request)
    {
        $launch = LtiMessageLaunch::new(
            $this->database,
            $this->cache,
            $this->cookie,
            $this->connector,
        );

        try {
            return $launch->initialize($request);
        } catch (LtiException $e) {
            // something?
            throw $e;
        }
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
        $title = $requestData['title'] ?? 'SmartyCards Practice';
        $description = $requestData['description'] ?? '';

        if (!$deckId) {
            throw new \InvalidArgumentException('Deck ID is required for deep link response');
        }

        // Create the resource that will be inserted into the LMS
        $resource = Resource::new()
            ->setUrl(route('lti.launch'))
            ->setTitle($title)
            ->setText($description)
            ->setCustomParams([
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
     * Submit a grade via AGS service
     * (Assignments and Grades Service)
     */
    public function submitGrade(LtiMessageLaunch $launch, float $score, string $userId)
    {
        if (!$launch->hasAgs()) {
            throw new \Exception('Assignments and Grades service not available');
        }

        $ags = $launch->getAgs();

        $grade = LtiGrade::new()
            ->setScoreGiven($score)
            ->setScoreMaximum(100)
            ->setUserId($userId)
            ->setTimestamp(date('c'))
            ->setActivityProgress('Completed')
            ->setGradingProgress('FullyGraded');

        return $ags->putGrade($grade);
    }


    /**
     * Retrieve grades via AGS service
     * (Assignments and Grades Service)
     */
    public function getGrades(LtiMessageLaunch $launch, ?string $userId)
    {
        if (!$launch->hasAgs()) {
            throw new \Exception('Assignments and Grades service not available');
        }

        $ags = $launch->getAgs();
        return $ags->getGrades(null, $userId);
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
     * @param array $launchData
     * @return array - validated data
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

        // Laravel will throw if invalid
        return Validator::make($launchData, $rules)->validate();
    }

    /**
     * Authenticate a user from an LTI launch
     * Creates a new user if one doesn't exist
     */
    public function authenticateFromLaunch(LtiMessageLaunch $launch): User
    {
        $launchData = $launch->getLaunchData();

        // $validated = $launchData;
        $validated = $this->validateLtiLaunchData($launchData);

        $ltiSubId = $validated['sub'];
        $ltiSisId = $validated[LtiConstants::LIS]['person_sourcedid'];
        $ltiEmail = $validated['email'];
        $ltiFirstName = $validated['given_name'] ?? '';
        $ltiLastName = $validated['family_name'] ?? '';

        // CASE 1: User has launched before.
        // - they exist in the db
        // - they have a subject id associated with their user
        $user = User::where('lti_sub_id', $ltiSubId)->first();

        // CASE 2: User has an account, but hasn't launched before.
        // - they exist in the db
        // - they do NOT have a subject id associated with their user
        if (!$user) {
            $user = User::where('emplid', $ltiSisId)->first();
        }

        // CASE 3: User does not exist at all.
        if (!$user) {
            // create new user
            $user = User::create([
                'lti_sub_id' => $ltiSubId,
                'emplid' => $ltiSisId,
                'umndid' => explode('@', $ltiEmail)[0],
                'email' => $ltiEmail,
                'first_name' => $ltiFirstName,
                'last_name' => $ltiLastName,
                // TODO: remove distinct name field
                'name'  => trim($ltiFirstName . ' ' . $ltiLastName),
                'password' => Hash::make(Str::random(32)), // random password
            ]);
        } else {
            // and update user info if any fields are null
            $user->update([
                'email' => $user->email ?? $ltiEmail,
                'first_name' => $user->first_name ?? $ltiFirstName,
                'last_name' => $user->last_name ?? $ltiLastName,
                'emplid' => $user->emplid ?? $ltiSisId,
                'lti_sub_id' => $user->lti_sub_id ?? $ltiSubId,
            ]);
        }

        // Log the user in
        Auth::login($user);
        return $user;
    }
}
