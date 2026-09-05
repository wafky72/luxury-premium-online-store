<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Jobs\SyncEventToFacebookCAPI;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Ingest events from the frontend for server-side tracking (CAPI / GTM fallback).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string',
            'product_id' => 'nullable|integer',
            'order_id'   => 'nullable|integer',
            'payload'    => 'nullable|array',
            'source'     => 'nullable|string',
            'value'      => 'nullable|numeric',
        ]);

        $eventId = (string) Str::uuid();
        $session = $request->header('X-Session-ID');
        $user = $request->user('sanctum');

        // Save to local database for internal analytics
        $event = Event::create([
            'name'       => $validated['event_name'],
            'user_id'    => $user?->id,
            'session_id' => $session,
            'product_id' => $validated['product_id'],
            'order_id'   => $validated['order_id'],
            'payload'    => $validated['payload'] ?? [],
            'source'     => $validated['source'] ?? 'website',
            'event_id'   => $eventId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        // Format payload for Facebook CAPI
        $capiPayload = [
            'event_name' => $validated['event_name'],
            'event_time' => time(),
            'event_id'   => $eventId,
            'user_phone' => $user?->phone ? hash('sha256', $user->phone) : null,
            'user_email' => $user?->email ? hash('sha256', $user->email) : null,
            'value'      => $validated['value'],
            'currency'   => 'BDT',
            'product_ids'=> $validated['product_id'] ? [$validated['product_id']] : [],
        ];

        // Dispatch job to tracking queue if CAPI is configured
        if (config('services.facebook.pixel_id')) {
            dispatch(new SyncEventToFacebookCAPI($capiPayload))->onQueue('tracking');
        }

        return response()->json([
            'success' => true,
            'message' => 'Event tracked successfully',
            'event_id' => $eventId,
        ]);
    }
}
