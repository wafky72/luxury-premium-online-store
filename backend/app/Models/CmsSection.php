<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsSection extends Model
{
    protected $fillable = ['page_id', 'type', 'props_json', 'sort_order', 'is_active', 'label'];

    protected $casts = [
        'props_json' => 'array',
        'is_active'  => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class, 'page_id');
    }

    /** Shape returned to the React frontend */
    public function toApiArray(): array
    {
        return [
            'type'  => $this->type,
            'order' => $this->sort_order,
            'props' => $this->props_json ?? [],
        ];
    }
}
