<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Fetch public configurations like store phone, email, links, etc.
     */
    public function public()
    {
        // Example: pulling settings from DB where group is 'public'
        $settings = DB::table('settings')->where('is_public', true)->pluck('value', 'key');

        // Fallback or defaults if DB table is empty
        if ($settings->isEmpty()) {
            $settings = [
                'store_name' => 'Tory Crown',
                'contact_email' => 'support@torycrown.com',
                'contact_phone' => '+880 123 456 7890',
                'social_facebook' => 'https://facebook.com/torycrown',
                'social_instagram' => 'https://instagram.com/torycrown',
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
}
