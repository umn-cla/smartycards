<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\DeckMembership;
use App\Services\Lti\LtiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;

class LtiController extends Controller
{
    const DECK_PRACTICE_ACTIVITY = 'practice';

    const DECK_QUIZ_ACTIVITY = 'quiz';

    const DECK_MATCHING_ACTIVITY = 'matching';

    const MISSING_LAUNCH_ID_MESSAGE = 'No launch ID found. Please try launching again from Canvas.';

    private function handleException(\Exception $e, ?string $userMessage = null): RedirectResponse
    {
        // In development, always throw exceptions for full debugging
        if (config('app.debug')) {
            throw $e;
        }

        // In production, log the exception for debugging
        \Log::error('LTI Error', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()->route('lti.error', [
            'message' => $userMessage ?? $e->getMessage(),
        ]);
    }

    /**
     * Handle OIDC login initiation from LMS
     */
    public function login(Request $request, LtiService $ltiService): RedirectResponse
    {
        return $ltiService->login(
            $request->all(),
            route('lti.launch')
        );
    }

    /**
     * Handle LTI launch and route to appropriate handler
     */
    public function launch(Request $request, LtiService $ltiService): RedirectResponse
    {
        try {
            $launch = $ltiService->validateAndCacheLaunch($request->all());

            // Authenticate the user from the LTI launch
            $ltiService->authenticateFromLaunch($launch);

            // Get launch ID to pass to subsequent requests
            $launchId = $launch->getLaunchId();

            // faculty set up assignment
            if ($launch->isDeepLinkLaunch()) {
                return redirect()->route('lti.deep_link', [
                    'launch_id' => $launchId,
                    'launch_type' => 'deep_link',
                ]);
            }

            // student (or faculty) launches assignment
            if ($launch->isResourceLaunch()) {
                return redirect()->route('lti.resource', [
                    'launch_id' => $launchId,
                ]);
            }

            throw new LtiException('Unknown launch type');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log the validation errors with structured data
            \Log::error('LTI Validation Error', [
                'validation_errors' => $e->errors(),
                'message' => $e->getMessage(),
            ]);

            return $this->handleException(
                $e,
                'Your Canvas user account is missing required information (email or student ID). Please contact your Canvas administrator.'
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Show deep link selection interface
     * Instructors use this to select a deck and configure the assignment
     */
    public function deepLink(Request $request, LtiService $ltiService): View|RedirectResponse
    {
        $launchId = $request->query('launch_id');
        if (!$launchId) {
            return $this->handleException(new LtiException(self::MISSING_LAUNCH_ID_MESSAGE));
        }

        try {
            $launch = $ltiService->getLaunchFromCache($launchId);

            return view('lti.deep_link', [
                'launch' => $launch,
                'launch_id' => $launchId,
                'settings' => $launch->getDeepLink()->settings(),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Handle deep link selection submission and return to LMS
     */
    public function deepLinkResponse(Request $request, LtiService $ltiService): View|RedirectResponse
    {
        $launchId = $request->input('launch_id');

        try {
            $response = $ltiService->createDeepLinkResponse($launchId, $request->all());

            // Return auto-submit form that posts back to LMS
            return view('lti.auto_submit', [
                'jwt' => $response['jwt'],
                'return_url' => $response['return_url'],
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Handle resource launch (student clicks on assignment)
     */
    public function resource(Request $request, LtiService $ltiService): RedirectResponse
    {
        $launchId = $request->query('launch_id');
        if (!$launchId) {
            return $this->handleException(new LtiException(self::MISSING_LAUNCH_ID_MESSAGE));
        }

        try {
            $user = Auth::user();
            $launch = $ltiService->getLaunchFromCache($launchId);
            $launchData = $launch->getLaunchData();

            // Get the custom parameters set during deep linking
            $customParams = $launchData[LtiConstants::CUSTOM] ?? [];
            $deckId = $customParams['deck_id'] ?? null;
            $deckActivity = $customParams['deck_activity'] ?? self::DECK_PRACTICE_ACTIVITY;

            $deck = Deck::findOrFail($deckId);
            $membershipRole = $ltiService->hasStaffRole($launch)
                ? DeckMembership::ROLE_EDITOR
                : DeckMembership::ROLE_VIEWER;

            $deck->addOrPromoteUserToRole($user, $membershipRole);

            // Create or update the LTI resource link with AGS endpoints
            $resourceLink = $ltiService->createOrUpdateResourceLink($launch, $deckId);

            // Create or update entry to track user's role and score for this Canvas assignment
            $entry = $ltiService->createOrUpdateEntry($launch, $user, $resourceLink);

            return redirect("/decks/{$deckId}/activities/{$deckActivity}/embed?lti_launch=true");
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Display LTI error page
     */
    public function error(Request $request): View
    {
        $message = $request->input('message', 'An error occurred during LTI authentication');

        return view('lti.error', [
            'message' => $message,
        ]);
    }

    /**
     * Return LTI 1.3 configuration JSON for Canvas auto-configuration
     *
     * Canvas can fetch this URL when creating a Developer Key to auto-populate all settings.
     * Usage: Admin → Developer Keys → "+ LTI Key" → "Enter URL" → paste this endpoint's URL
     */
    public function config(): \Illuminate\Http\JsonResponse
    {
        $appUrl = config('app.url');

        $configuration = [
            'title' => 'SmartyCards',
            'description' => 'Collaborative flashcards',
            'oidc_initiation_url' => route('lti.login'),
            'target_link_uri' => route('lti.launch'),
            'scopes' => [
                'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                'https://purl.imsglobal.org/spec/lti-ags/scope/score',
                'https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly',
            ],
            'extensions' => [
                [
                    'platform' => 'canvas.instructure.com',
                    'privacy_level' => 'public',
                    'settings' => [
                        'platform' => 'canvas.instructure.com',
                        'placements' => [
                            [
                                'placement' => 'assignment_selection',
                                'message_type' => 'LtiDeepLinkingRequest',
                                'target_link_uri' => route('lti.launch'),
                                'text' => 'SmartyCards',
                                'icon_url' => "{$appUrl}/favicon.ico",
                                'enabled' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'public_jwk_url' => route('lti.keys'),
            'custom_fields' => [],
        ];

        return response()->json($configuration)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
}
