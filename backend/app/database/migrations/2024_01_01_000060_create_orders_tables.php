<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('value', 10, 2); // amount or percent
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->decimal('max_discount', 10, 2)->nullable(); // cap for percent type
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_user_limit')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['code', 'is_active', 'expires_at']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // TC-2024-00001
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();

            // Status lifecycle
            $table->enum('status', [
                'pending', 'confirmed', 'processing',
                'shipped', 'delivered', 'cancelled',
                'returned', 'refunded', 'flagged'
            ])->default('pending')->index();

            // Shipping address (snapshot, denormalized)
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_district');

            // Financials
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Payment
            $table->enum('payment_method', ['cod', 'bkash', 'nagad', 'sslcommerz'])->default('cod');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->decimal('advance_paid', 10, 2)->default(0); // for partial COD

            // Courier
            $table->string('courier')->nullable(); // steadfast, pathao
            $table->string('courier_service')->nullable(); // standard, express
            $table->decimal('courier_charge', 10, 2)->default(0);

            // Fraud & Metadata
            $table->unsignedTinyInteger('fraud_score')->default(0); // 0-100
            $table->json('fraud_data')->nullable();
            $table->text('notes')->nullable();
            $table->string('source')->default('web'); // web, app, admin
            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'payment_status', 'created_at']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();

            // Snapshots at order time
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->string('sku');
            $table->string('karat')->nullable();
            $table->decimal('weight_grams', 8, 3)->nullable();
            $table->string('image_url')->nullable();

            $table->unsignedSmallInteger('qty');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();

            $table->index('order_id');
        });

        Schema::create('order_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->text('note')->nullable();
            $table->string('created_by')->nullable(); // 'system', admin name, etc.
            $table->timestamp('created_at');

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_timeline');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('coupons');
    }
};
