<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationsLog extends Model
{
    protected $table = 'notifications_log';

    protected $fillable = [
        'channel', 'recipient', 'template', 'content', 'status',
        'error', 'order_id', 'user_id', 'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
