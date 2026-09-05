<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Dynamic gold rate pricing engine
        Schema::create('gold_rates', function (Blueprint $table) {
            $table->id();
            $table->enum('karat', ['18K', '21K', '22K', '24K']);
            $table->decimal('price_per_gram', 10, 2); // BDT per gram
            $table->date('effective_date');
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['karat', 'effective_date', 'is_active']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('full_description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('collection_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->decimal('gold_rate_override', 10, 2)->nullable(); // optional fixed price bypass
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('tags')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug', 'is_active', 'is_featured']);
            $table->index(['category_id', 'collection_id']);
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('name')->nullable(); // e.g. "18K · Size 7 · 3.2g"
            $table->enum('karat', ['18K', '21K', '22K', '24K'])->nullable();
            $table->string('size')->nullable();          // ring size, bracelet length
            $table->string('color')->nullable();         // gold color (yellow/white/rose)
            $table->decimal('weight_grams', 8, 3)->nullable(); // precision for pricing
            $table->string('stone_type')->nullable();    // Diamond, Ruby, Emerald…
            $table->decimal('stone_weight', 8, 3)->nullable(); // carats
            $table->decimal('making_charge', 10, 2)->default(0); // BDT, added on top
            $table->decimal('base_price_override', 10, 2)->nullable(); // bypass formula
            $table->unsignedInteger('stock_qty')->default(0);
            $table->unsignedInteger('reserved_qty')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'is_active']);
            $table->index('stock_qty');
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('url');
            $table->string('alt_text')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'is_primary', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('gold_rates');
    }
};
