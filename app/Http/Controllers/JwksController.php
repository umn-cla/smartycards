<?php

namespace App\Http\Controllers;

use App\Services\Lti\LtiService;

class JwksController extends Controller
{
    public function keys(LtiService $ltiService)
    {
        return response()->json($ltiService->getPublicJwks());
    }
}
