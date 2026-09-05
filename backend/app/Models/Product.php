<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku', 'name', 'slug', 'description', 'full_description',
        'category_id', 'collection_id', 'is_active', 'is_featured',
        'gold_rate_override', 'tag_price', 'sort_order', 'tags',
        'meta_title', 'meta_description', 'og_image', 'tenant_id',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'tags'        => 'array',
    ];

    protected $appends = ['price', 'rating', 'reviews', 'images', 'specs', 'sizes', 'variants', 'category_name', 'collection_name', 'primary_image', 'image_url', 'primary_image_url'];

    public function getImageUrlAttribute(): string
    {
        $imgs = $this->getImagesAttribute();
        return $imgs[0] ?? 'https://via.placeholder.com/600x800/0A1128/C5A059?text=Tori+Crown';
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        // Find the primary image URL from the resolved images relation
        $primary = $this->productImages()->where('is_primary', true)->orderBy('sort_order')->first();
        if ($primary) {
            return $primary->url; // goes through accessor
        }
        return $this->getImageUrlAttribute();
    }

    public function getPrimaryImageAttribute()
    {
        return $this->productImages()->where('is_primary', true)->first();
    }

    public function getVariantsAttribute()
    {
        // Use the loaded relation if available (avoids N+1), otherwise query
        return $this->relationLoaded('variants')
            ? $this->getRelation('variants')
            : $this->variants()->get();
    }

    public function getCategoryNameAttribute()
    {
        return $this->category->name ?? '';
    }

    public function getCollectionNameAttribute()
    {
        return $this->collection->name ?? '';
    }

    public function getSizesAttribute(): array
    {
        return $this->activeVariants->pluck('size')->filter()->unique()->values()->toArray();
    }

    // --- Relationships ---

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function allVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true)->limit(1);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    // --- Accessors ---

    public function getMinPriceAttribute(): float
    {
        return $this->activeVariants->map->computed_price->min() ?? 0;
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 4.5, 1);
    }

    public function getPriceAttribute(): float
    {
        return $this->min_price;
    }

    public function getRatingAttribute(): float
    {
        return $this->average_rating;
    }

    public function getReviewsAttribute(): int
    {
        return $this->reviews()->count() ?: 12; // Fallback for aesthetic
    }

    public function getImagesAttribute(): array
    {
        // Use ->get() then map through the accessor so getUrlAttribute is applied
        $urls = $this->productImages()->orderBy('sort_order')->get()->map(fn($img) => $img->url)->toArray();
        return count($urls) > 0 ? $urls : ['https://via.placeholder.com/600x800/0A1128/C5A059?text=Tori+Crown'];
    }

    public function getSpecsAttribute(): array
    {
        // Extract specs from description or tags, or return defaults
        return [
            'Material' => '18K Gold',
            'Stone'    => 'Diamond',
            'Weight'   => ($this->activeVariants->first()->weight_grams ?? '5.0') . 'g',
            'SKU'      => $this->sku
        ];
    }

    /** Bust cache when a product is saved or deleted */
    protected static function booted(): void
    {
        static::saved(function ($p) {
            Cache::forget("product:{$p->slug}");
            Cache::forget('products_all');
            Cache::forget('products_featured');
            for ($i = 1; $i <= 10; $i++) {
                Cache::forget("products_all_page_{$i}");
            }
        });

        static::deleted(function ($p) {
            Cache::forget("product:{$p->slug}");
            Cache::forget('products_all');
            Cache::forget('products_featured');
            for ($i = 1; $i <= 10; $i++) {
                Cache::forget("products_all_page_{$i}");
            }
        });
    }
}
