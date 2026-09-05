<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('gateway', ['cod', 'bkash', 'nagad', 'sslcommerz']);
            $table->string('transaction_id')->nullable()->index();
            $table->string('gateway_order_id')->nullable(); // gateway's own reference
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->json('gateway_response')->nullable(); // full raw response stored
            $table->string('payment_method_detail')->nullable(); // e.g. bKash number
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['gateway', 'status']);
        });

        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');      // Steadfast, Pathao
            $table->string('slug')->unique();
            $table->text('api_key_encrypted')->nullable(); // Laravel encrypt()
            $table->text('api_secret_encrypted')->nullable();
            $table->string('base_url')->nullable();
            $table->json('config')->nullable(); // extra gateway-specific config
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('courier_slug'); // denormalized
            $table->string('tracking_number')->nullable()->index();
            $table->string('consignment_id')->nullable()->index(); // courier's ID
            $table->enum('status', [
                'pending', 'picked_up', 'in_transit',
                'out_for_delivery', 'delivered', 'returned', 'failed'
            ])->default('pending');
            $table->decimal('charge', 10, 2)->default(0);
            $table->date('estimated_delivery')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('webhook_data')->nullable(); // latest courier webhook payload
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('couriers');
        Schema::dropIfExists('payments');
    }
};
