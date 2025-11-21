<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\DeckMembership;
use App\Services\Lti\LtiService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiMessageLaunch;

class LtiController extends Controller
{
    const DECK_PRACTICE_ACTIVITY = 'practice';
    const DECk_QUIZ_ACTIVITY = 'quiz';
    const DECK_MATCHING_ACTIVITY = 'matching';

    /**
     * Handle OIDC login initiation from LMS
     */
    public function login(Request $request, LtiService $ltiService)
    {
        debug('LTI Login', [
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
        debug('LTI Login', [
            'request' => $request->all()
        ]);

        try {
            $launch = $ltiService->validateAndCacheLaunch($request->all());

            debug('LTI launch', [
                'launch_id' => $launch->getLaunchId(),
                'launch' => $launch->getLaunchData()
            ]);

            // Authenticate the user from the LTI launch
            $user = $ltiService->authenticateFromLaunch($launch);

            debug('LTI user authenticated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ]);

            // Get launch ID to pass to subsequent requests
            $launchId = $launch->getLaunchId();

            // faculty set up assignment
            if ($launch->isDeepLinkLaunch()) {
                return redirect()->route('lti.deep_link', ['launch_id' => $launchId]);
            }

            // student (or faculty) launches assignment
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
        debug('LTI deep link', ['request' => $request->all()]);

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
     * Gets a list of user roles from LTI launch
     *
     * @param mixed $launch The LTI launch object
     * @return array List of roles (URIs) like
     * "http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor"
     */
    private function getRolesFromLaunch(LtiMessageLaunch $launch): array
    {
        $launchData = $launch->getLaunchData();
        return $launchData[LtiConstants::ROLES] ?? [];
    }

    private function doesLaunchUserHaveStaffRole(LtiMessageLaunch $launch)
    {
        $editorRoles = [
            LtiConstants::INSTITUTION_ADMINISTRATOR,
            LtiConstants::MEMBERSHIP_INSTRUCTOR,
            LtiConstants::MEMBERSHIP_TA,
            LtiConstants::MEMBERSHIP_CONTENTDEVELOPER,
        ];

        $roles = $this->getRolesFromLaunch($launch);

        return !empty(array_intersect($roles, $editorRoles));
    }

    /**
     * Handle resource launch (student clicks on assignment)
     */
    public function resource(Request $request, LtiService $ltiService)
    {
        $user = Auth::user();

        // Get launch ID from query parameter
        $launchId = $request->query('launch_id');
        if (!$launchId) {
            return redirect()->route('lti.error', [
                'message' => 'No launch ID found. Please try launching again from Canvas.'
            ]);
        }

        try {
            $launch = $ltiService->getLaunchFromCache($launchId);
            $launchData = $launch->getLaunchData();

            // Get the custom parameters set during deep linking
            $customParams = $launchData[LtiConstants::CUSTOM] ?? [];
            $deckId = $customParams['deck_id'] ?? null;
            $deckActivity = $customParams['deck_activity'] ?? self::DECK_PRACTICE_ACTIVITY;

            $deck = Deck::findOrFail($deckId);
            $membershipRole = $this->doesLaunchUserHaveStaffRole($launch)
                ? DeckMembership::ROLE_EDITOR
                : DeckMembership::ROLE_VIEWER;

            $deck->addOrPromoteUserToRole($user, $membershipRole);

            // Create or update the LTI resource link with AGS endpoints
            $ltiService->createOrUpdateResourceLink($launch, $deckId);

            return redirect("/decks/{$deckId}/activities/{$deckActivity}/embed?lti_launch_id={$launchId}");
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
