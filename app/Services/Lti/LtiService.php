<?php

namespace App\Services\Lti;

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
}
