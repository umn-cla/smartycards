<?php

namespace App\Services\Lti;

use App\Models\LtiPlatform;
use App\Models\LtiDeployment;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiDeployment;
use Packback\Lti1p3\LtiRegistration;
use Packback\Lti1p3\LtiDeployment as PackbackLtiDeployment;

class LtiDatabase implements IDatabase
{
    /**
     * Find an LTI registration (platform + deployment) by issuer and optional client ID
     *
     * This is called during the OAuth flow to look up the platform config
     */
    public function findRegistrationByIssuer(string $iss, ?string $clientId = null): ?ILtiRegistration
    {
        // Find the platform by issuer
        $platform = LtiPlatform::findByIssuer($iss);

        if (!$platform) {
            return null;
        }

        // Find deployment by client_id (if provided)
        $deployment = $platform->deployments()
            ->when($clientId, function ($query, $clientId) {
                return $query->where('client_id', $clientId);
            })
            ->first();

        if (!$deployment) {
            return null;
        }

        return $this->buildRegistration($platform, $deployment);
    }

    public function findDeployment(string $iss, string $deploymentId, ?string $clientId = null): ?ILtiDeployment
    {
        // Find the platform by issuer
        $platform = LtiPlatform::findByIssuer($iss);

        if (!$platform) {
            return null;
        }

        $deployment = $platform->deployments()
            ->where('deployment_id', $deploymentId)
            ->when($clientId, function ($query, $clientId) {
                return $query->where('client_id', $clientId);
            })
            ->first();

        if (!$deployment) {
            return null;
        }

        return PackbackLtiDeployment::new($deployment->deployment_id);
    }


    /**
     * Build an LtiRegistration object from our Eloquent models
     */
    private function buildRegistration(LtiPlatform $platform, LtiDeployment $deployment): ILtiRegistration
    {
        $ltiPrivateKey = config('lti.private_key');
        if (!$ltiPrivateKey) {
            throw new \RuntimeException('LTI private key not configured');
        }

        $ltiKid = config('lti.kid');
        if (!$ltiKid) {
            throw new \RuntimeException('LTI KID not configured');
        }

        return LtiRegistration::new([
            'issuer' => $platform->issuer,
            'clientId' => $deployment->client_id,
            'keySetUrl' => $platform->key_set_url,
            'authTokenUrl' => $platform->auth_token_url,
            'authLoginUrl' => $platform->auth_login_url,
            'toolPrivateKey' => $ltiPrivateKey,
            'kid' => $ltiKid,
        ]);
    }
}
