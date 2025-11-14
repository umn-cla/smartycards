<?php

namespace App\Http\Controllers;

use App\Services\Lti\LtiService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Packback\Lti1p3\LtiException;

class LtiController extends Controller
{
    /**
     * Handle OIDC login initiation from LMS
     */
    public function login(Request $request, LtiService $ltiService)
    {
        info('LTI Login', [
            'request' => $request->all()
        ]);

        return $ltiService->login(
            $request->all(),
            route('lti.launch')
        );
    }

    /**
     * Handle LTI launch and route to appropriate handler
     */
    public function launch(Request $request, LtiService $ltiService)
    {
        info('LTI Login', [
            'request' => $request->all()
        ]);

        try {
            $launch = $ltiService->validateLaunch($request->all());


            info('LTI launch', [
                'request' => $request->all(),
                'launch_id' => $launch->getLaunchId(),
                'launch' => $launch->getLaunchData()
            ]);

            // Get launch ID to pass to subsequent requests
            $launchId = $launch->getLaunchId();

            if ($launch->isDeepLinkLaunch()) {
                return redirect()->route('lti.deep_link', ['launch_id' => $launchId]);
            }

            if ($launch->isResourceLaunch()) {
                return redirect()->route('lti.resource', ['launch_id' => $launchId]);
            }

            if ($launch->isSubmissionReviewLaunch()) {
                return redirect()->route('lti.submission_review', ['launch_id' => $launchId]);
            }

            throw new LtiException('Unknown launch type');
        } catch (LtiException $e) {
            if (config('app.debug')) {
                throw $e;
            }

            return redirect()->route('lti.error', [
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show deep link selection interface
     * Instructors use this to select a deck and configure the assignment
     */
    public function deepLink(Request $request, LtiService $ltiService)
    {
        Debugbar::info('LTI deep link', ['request' => $request->all()]);

        // Get launch ID from query parameter
        $launchId = $request->query('launch_id');

        if (!$launchId) {
            return redirect()->route('lti.error', [
                'message' => 'No launch ID found. Please try launching again from Canvas.'
            ]);
        }

        try {
            $launch = $ltiService->getLaunchFromCache($launchId);

            return view('lti.deep_link', [
                'launch' => $launch,
                'launch_id' => $launchId,
                'settings' => $launch->getDeepLink()->settings()
            ]);
        } catch (\Exception $e) {
            if (config('app.debug')) {
                throw $e;
            }

            return redirect()->route('lti.error', [
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle deep link selection submission and return to LMS
     */
    public function deepLinkResponse(Request $request, LtiService $ltiService)
    {
        // Get launch ID from request body
        $launchId = $request->input('launch_id');

        if (!$launchId) {
            return redirect()->route('lti.error', [
                'message' => 'No launch ID found. Please try launching again from Canvas.'
            ]);
        }

        try {
            $response = $ltiService->createDeepLinkResponse($launchId, $request->all());

            // Return auto-submit form that posts back to LMS
            return view('lti.auto_submit', [
                'jwt' => $response['jwt'],
                'return_url' => $response['return_url']
            ]);
        } catch (\Exception $e) {
            if (config('app.debug')) {
                throw $e;
            }

            return redirect()->route('lti.error', [
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle resource launch (student clicks on assignment)
     */
    public function resource(Request $request, LtiService $ltiService)
    {
        // Get launch ID from query parameter
        $launchId = $request->query('launch_id');

        if (!$launchId) {
            return redirect()->route('lti.error', [
                'message' => 'No launch ID found. Please try launching again from Canvas.'
            ]);
        }

        try {
            $launch = $ltiService->getLaunchFromCache($launchId);

            // Get the custom parameters set during deep linking
            $customParams = $launch->getLaunchData()['https://purl.imsglobal.org/spec/lti/claim/custom'] ?? [];
            $deckId = $customParams['deck_id'] ?? null;

            if (!$deckId) {
                throw new \Exception('No deck configured for this assignment');
            }

            // Redirect to the Vue app with the deck
            return redirect("/practice/{$deckId}?lti_launch_id={$launchId}");
        } catch (\Exception $e) {
            if (config('app.debug')) {
                throw $e;
            }

            return redirect()->route('lti.error', [
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle submission review launch (instructor reviews student work)
     */
    public function submissionReview(Request $request, LtiService $ltiService)
    {
        // Get launch ID from query parameter
        $launchId = $request->query('launch_id');

        if (!$launchId) {
            return redirect()->route('lti.error', [
                'message' => 'No launch ID found. Please try launching again from Canvas.'
            ]);
        }

        try {
            $launch = $ltiService->getLaunchFromCache($launchId);

            // Get user ID being reviewed
            $forUser = $launch->getLaunchData()['https://purl.imsglobal.org/spec/lti/claim/for_user'] ?? null;

            // Redirect to appropriate review interface
            return view('lti.submission_review', [
                'launch' => $launch,
                'for_user' => $forUser
            ]);
        } catch (\Exception $e) {
            if (config('app.debug')) {
                throw $e;
            }

            return redirect()->route('lti.error', [
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display LTI error page
     */
    public function error(Request $request)
    {
        $message = $request->input('message', 'An error occurred during LTI authentication');

        return view('lti.error', [
            'message' => $message,
        ]);
    }
}
