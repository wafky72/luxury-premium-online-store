<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collection extends Model
{
    protected $fillable = ['name', 'slug', 'image', 'banner_image', 'description', 'sort_order', 'is_active', 'meta_title', 'meta_description'];
    protected $casts = ['is_active' => 'boolean'];

    public function products(): HasMany { return $this->hasMany(Product::class); }
}
