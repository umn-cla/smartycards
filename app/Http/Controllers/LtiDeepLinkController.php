<?php

namespace App\Http\Controllers;

use App\Services\Lti\LtiService;
use Illuminate\Http\Request;

class LtiDeepLinkController extends Controller
{
    public function index(Request $request, LtiService $ltiService)
    {
        $launchId = session('lti_launch_id');

        try {
            $launch = $ltiService->getLaunchFromCache($launchId);

            return view('lti.deep_link', [
                'launch' => $launch,
                'settings' => $launch->getDeepLink()->settings()
            ]);
        } catch (\Exception $e) {
            return redirect()->route('lti.error', [
                'message' => $e->getMessage()
            ]);
        }
    }

    public function response(Request $request, LtiService $ltiService)
    {
        $launchId = session('lti_launch_id');

        try {
            $response = $ltiService->createDeepLinkResponse($launchId);

            // Return a view with an auto-posting form
            return view('lti.auto_submit', [
                'jwt' => $response['jwt'],
                'return_url' => $response['return_url']
            ]);
        } catch (\Exception $e) {
            return redirect()->route('lti.error', [
                'message' => $e->getMessage()
            ]);
        }
    }
}
