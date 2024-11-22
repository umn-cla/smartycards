<?php

namespace App\Http\Controllers;

use App\Library\FeatureFlagService;

class FeatureFlagController extends Controller
{
    public function index(FeatureFlagService $featureFlagService)
    {
        return response()->json($featureFlagService->getAllFeatures());
    }
}
