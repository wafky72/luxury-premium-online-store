<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = [
        'name', 'slug', 'api_key_encrypted', 'api_secret_encrypted',
        'base_url', 'config', 'is_active'
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    /** Accessors for encrypted keys */
    public function getApiKeyAttribute($value)
    {
        return $this->api_key_encrypted ? decrypt($this->api_key_encrypted) : null;
    }

    public function getApiSecretAttribute($value)
    {
        return $this->api_secret_encrypted ? decrypt($this->api_secret_encrypted) : null;
    }
}
