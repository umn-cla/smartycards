<?php

namespace App\Http\Controllers;

use App\Services\Lti\LtiService;
use Illuminate\Http\Request;
use Packback\Lti1p3\LtiException;

class LtiLaunchController extends Controller
{
    public function launch(Request $request, LtiService $ltiService)
    {
        try {
            $launch = $ltiService->validateLaunch($request->all());

            // Store launch ID in session
            session(['lti_launch_id' => $launch->getLaunchId()]);

            if ($launch->isDeepLinkLaunch()) {
                return redirect()->route('lti.deep_link');
            }

            if ($launch->isResourceLaunch()) {
                return redirect()->route('lti.resource');
            }

            if ($launch->isSubmissionReviewLaunch()) {
                return redirect()->route('lti.submission_review');
            }

            throw new LtiException('Unknown launch type');
        } catch (LtiException $e) {
            // just rethrow in debug mode for easier troubleshooting
            if (config('app.debug')) {
                throw $e;
            }

            return redirect()->route('lti.error', [
                'message' => $e->getMessage()
            ]);
        }
    }
}
