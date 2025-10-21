<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LtiService;
use Packback\Lti1p3\LtiException;

class LtiController extends Controller
{

    function login(Request $request)
    {
        dd($request->json()->all());
    }

    function launch(Request $request)
    {
        // handle launch request
    }

    function jwks(Request $request)
    {
        // share jwks keys
    }
}
