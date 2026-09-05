<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class CmsPage extends Model
{
    protected $fillable = [
        'title', 'slug', 'status', 'published_at', 'is_home',
        'meta_title', 'meta_description', 'og_image', 'tenant_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_home'      => 'boolean',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(CmsSection::class, 'page_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function allSections(): HasMany
    {
        return $this->hasMany(CmsSection::class, 'page_id')->orderBy('sort_order');
    }

    public function publish(): void
    {
        $this->update(['status' => 'published', 'published_at' => now()]);
        Cache::forget("cms:page:{$this->slug}");
    }

    public function unpublish(): void
    {
        $this->update(['status' => 'draft']);
        Cache::forget("cms:page:{$this->slug}");
    }

    protected static function booted(): void
    {
        static::saved(fn($p) => Cache::forget("cms:page:{$p->slug}"));
        static::deleted(fn($p) => Cache::forget("cms:page:{$p->slug}"));
    }
}
