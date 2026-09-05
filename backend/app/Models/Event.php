<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public $timestamps = false; // migration has manually added created_at

    protected $fillable = [
        'name', 'user_id', 'session_id', 'product_id', 'order_id',
        'event_id', 'payload', 'source', 'ip_address', 'user_agent',
        'synced_to_fb', 'synced_at', 'created_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'synced_to_fb' => 'boolean',
        'synced_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
