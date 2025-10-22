<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LtiErrorController extends Controller
{
    public function index(Request $request)
    {
        $message = $request->input('message', 'An error occurred during LTI authentication');

        return view('lti_error', [
            'message' => $message,
        ]);
    }
}
