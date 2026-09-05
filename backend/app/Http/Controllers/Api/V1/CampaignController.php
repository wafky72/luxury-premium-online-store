<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;

class CampaignController extends Controller
{
    /**
     * Get all active campaigns (Daily Offers, Best Deals, etc.)
     */
    public function index(): JsonResponse
    {
        $campaigns = Campaign::active()->get();

        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    /**
     * Get a specific campaign by type/slug
     */
    public function show(string $type): JsonResponse
    {
        $campaign = Campaign::active()
            ->where('type', $type)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $campaign
        ]);
    }
}
