<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Event tracking for Facebook CAPI, GTM, analytics
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // view_product, add_to_cart, purchase…
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_id')->nullable(); // deduplication ID for FB CAPI
            $table->json('payload')->nullable(); // full event data
            $table->string('source')->default('web'); // web, app
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('synced_to_fb')->default(false);
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('created_at');

            $table->index(['name', 'created_at']);
            $table->index(['synced_to_fb', 'name']);
        });

        // SMS & Email notification log
        Schema::create('notifications_log', function (Blueprint $table) {
            $table->id();
            $table->enum('channel', ['sms', 'email']);
            $table->string('recipient'); // phone or email
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('template'); // order_placed, shipped, etc.
            $table->text('content');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['channel', 'status', 'created_at']);
        });

        // Global settings key-value store
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('group')->default('general'); // general, payment, sms, shipping
            $table->enum('type', ['string', 'json', 'boolean', 'integer'])->default('string');
            $table->string('label')->nullable(); // admin-facing
            $table->boolean('is_public')->default(false); // expose to frontend API
            $table->timestamps();

            $table->index(['group', 'is_public']);
        });

        // Feature flags for controlled rollout
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->boolean('is_enabled')->default(false);
            $table->unsignedTinyInteger('rollout_percent')->default(100);
            $table->json('conditions')->nullable(); // e.g. {"role": "admin"}
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Webhook incoming event log
        Schema::create('webhooks_log', function (Blueprint $table) {
            $table->id();
            $table->string('source'); // bkash, nagad, steadfast, pathao
            $table->string('event')->nullable();
            $table->json('payload');
            $table->enum('status', ['received', 'processed', 'failed'])->default('received');
            $table->text('error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('created_at');

            $table->index(['source', 'status']);
        });

        // Marketing campaigns
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['flash_sale', 'email_blast', 'sms_blast', 'banner'])->default('banner');
            $table->enum('status', ['draft', 'active', 'paused', 'ended'])->default('draft');
            $table->json('config')->nullable(); // campaign-specific config
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at']);
        });

        // Product reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reviewer_name');
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('body')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_verified_purchase')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'is_approved', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('webhooks_log');
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('notifications_log');
        Schema::dropIfExists('events');
    }
};
