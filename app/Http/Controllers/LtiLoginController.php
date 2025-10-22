<?php

namespace App\Http\Controllers;

use App\Services\Lti\LtiService;
use Illuminate\Http\Request;

class LtiLoginController extends Controller
{
    public function login(Request $request, LtiService $ltiService)
    {
        return $ltiService->login(
            $request->all(),
            route('lti.launch')
        );
    }
}
