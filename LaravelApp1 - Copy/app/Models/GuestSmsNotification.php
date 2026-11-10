<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestSmsNotification extends Model
{
    protected $table = 'guest_sms_notifications';
    protected $primaryKey = 'guest_sms_notification_id';

    protected $fillable = [
        'order_id',
        'message',
        'delivery_status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
