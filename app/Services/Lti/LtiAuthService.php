<?php

namespace App\Services\Lti;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Packback\Lti1p3\LtiMessageLaunch;

class LtiAuthService
{
    /**
     * Authenticate a user from an LTI launch
     * Creates a new user if one doesn't exist
     */
    public function authenticateFromLaunch(LtiMessageLaunch $launch): User
    {
        $launchData = $launch->getLaunchData();

        // Extract user ID from LTI (the 'sub' claim)
        $ltiUserId = $launchData['sub'] ?? null;

        if (!$ltiUserId) {
            throw new \Exception('LTI launch missing required user ID (sub claim)');
        }

        // Extract custom claims for SIS data
        $customClaims = $launchData['https://purl.imsglobal.org/spec/lti/claim/custom'] ?? [];
        $sisId = $customClaims['sis_id'] ?? null;

        // Try to find existing user by emplid (SIS ID) first, then by LTI user ID
        $user = null;
        if ($sisId) {
            $user = User::where('emplid', $sisId)->first();
        }

        if (!$user) {
            $user = User::where('lti_user_id', $ltiUserId)->first();
        }

        if (!$user) {
            // Create a new user
            $user = User::create([
                'lti_user_id' => $ltiUserId,
                'emplid' => $sisId,
                'umndid' => $sisId ?? $ltiUserId,
                'email' => "lti-{$ltiUserId}@canvas.local",
                'name' => "Canvas User",
                'password' => Hash::make(Str::random(32)),
            ]);
        } elseif ($sisId && !$user->lti_user_id) {
            // Update existing user with LTI user ID
            $user->update(['lti_user_id' => $ltiUserId]);
        }

        // Log the user in
        Auth::login($user);

        return $user;
    }
}
