<?php

// =====================================================
//  Tory Crown — API v1 Routes
//  All routes versioned under /api/v1/
// =====================================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CmsController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CollectionController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\SettingsController;
use App\Http\Controllers\Api\V1\CampaignController;

// ─── Public Routes ───────────────────────────────────────────────
Route::prefix('v1')->group(function () {
    
    // Debug route to diagnose CSRF/Session drops
    Route::get('/debug-session', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'is_secure' => $request->isSecure(),
            'scheme' => $request->getScheme(),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
            'session_domain' => config('session.domain'),
            'session_secure' => config('session.secure'),
            'headers' => $request->headers->all(),
        ]);
    })->middleware('web');

    // Campaigns (Offers, Deals)
    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::get('/campaigns/{type}', [CampaignController::class, 'show']);

    // CMS — dynamic page layout
    Route::get('/pages/{slug}', [CmsController::class, 'show']);
    Route::get('/global-sections', [CmsController::class, 'globalSections']);
    Route::get('/settings/public', [SettingsController::class, 'public']);

    // Product Catalog
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/products/{slug}/related', [ProductController::class, 'related']);
    Route::get('/products/{slug}/reviews', [ProductController::class, 'reviews']);
    Route::post('/products/search', [ProductController::class, 'search']);

    // Categories & Collections
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/collections', [CollectionController::class, 'index']);
    Route::get('/collections/{slug}', [CollectionController::class, 'show']);

    // Cart (session-based, no auth needed)
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add', [CartController::class, 'add']);
        Route::put('/items/{id}', [CartController::class, 'updateQty']);
        Route::delete('/items/{id}', [CartController::class, 'remove']);
        Route::post('/coupon', [CartController::class, 'applyCoupon']);
        Route::delete('/coupon', [CartController::class, 'removeCoupon']);
    });

    // Order tracking (public by order number)
    Route::get('/orders/{orderNumber}/track', [OrderController::class, 'track'])
        ->middleware('throttle:30,1');
        
    // Place a new order (public / guest checkout allowed)
    Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:10,1');

    // Auth
    Route::prefix('auth')->middleware('throttle:10,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // Payment Webhooks (must be public, gateway-initiated)
    Route::prefix('payments/webhook')->group(function () {
        Route::any('/bkash', [PaymentController::class, 'webhookBkash']);
        Route::any('/nagad', [PaymentController::class, 'webhookNagad']);
        Route::any('/sslcommerz', [PaymentController::class, 'webhookSslcommerz']);
    });

    // Courier Webhooks
    Route::any('/couriers/webhook/steadfast', [\App\Http\Controllers\Api\V1\CourierController::class, 'webhookSteadfast']);
    Route::any('/couriers/webhook/pathao', [\App\Http\Controllers\Api\V1\CourierController::class, 'webhookPathao']);

    // Event Tracking (frontend fires these)
    Route::post('/events', [EventController::class, 'store'])->middleware('throttle:120,1');

    // ─── Authenticated Routes ─────────────────────────────────────
    Route::middleware(['auth:sanctum'])->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

        // Payment initiation
        Route::post('/payments/initiate', [PaymentController::class, 'initiate'])->middleware('throttle:10,1');
        Route::get('/payments/{transactionId}/verify', [PaymentController::class, 'verify']);

        // Profile / Addresses
        Route::get('/profile', [\App\Http\Controllers\Api\V1\ProfileController::class, 'show']);
        Route::put('/profile', [\App\Http\Controllers\Api\V1\ProfileController::class, 'update']);
        Route::get('/addresses', [\App\Http\Controllers\Api\V1\ProfileController::class, 'addresses']);
        Route::post('/addresses', [\App\Http\Controllers\Api\V1\ProfileController::class, 'storeAddress']);
        Route::put('/addresses/{id}', [\App\Http\Controllers\Api\V1\ProfileController::class, 'updateAddress']);
        Route::delete('/addresses/{id}', [\App\Http\Controllers\Api\V1\ProfileController::class, 'destroyAddress']);
    });
});
