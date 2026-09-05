<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // CMS Pages — each page is a collection of ordered sections
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique(); // 'home', 'about', 'bridal'
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_home')->default(false);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug', 'status']);
        });

        // Sections within a specific page
        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('cms_pages')->cascadeOnDelete();
            $table->string('type'); // hero_split, product_grid, promo_banner, trust_strip…
            $table->json('props_json'); // flexible dynamic props for React component
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('label')->nullable(); // admin-facing label for identification
            $table->timestamps();

            $table->index(['page_id', 'sort_order', 'is_active']);
        });

        // Global reusable sections (shared across pages, e.g. trust strip, announcement)
        Schema::create('cms_global_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // 'trust_strip', 'announcement_bar'
            $table->string('type');
            $table->json('props_json');
            $table->boolean('is_active')->default(true);
            $table->string('label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_global_sections');
        Schema::dropIfExists('cms_sections');
        Schema::dropIfExists('cms_pages');
    }
};
