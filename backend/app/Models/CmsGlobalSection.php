<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsGlobalSection extends Model
{
    protected $fillable = ['key', 'type', 'props_json', 'is_active', 'label'];

    protected $casts = [
        'props_json' => 'array',
        'is_active'  => 'boolean',
    ];

    /** Shape returned to the React frontend */
    public function toApiArray(): array
    {
        return [
            'type'  => $this->type,
            'props' => $this->props_json ?? [],
        ];
    }
}
