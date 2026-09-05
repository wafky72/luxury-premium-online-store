<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $table = 'notifications_log';
    protected $fillable = ['channel', 'recipient', 'template', 'data', 'status', 'error'];

    protected $casts = [
        'data' => 'array',
    ];
}
