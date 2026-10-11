<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'reference_code',
        'customer_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'order_date',
        'pickup_date',
        'pickup_time',
        'status',
        'total_amount',
        'payment_method',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'order_date'     => 'datetime',
        'pickup_date'    => 'date',
        'total_amount'   => 'decimal:2',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /* -------------------------------------------------
     |  Relationships
     |-------------------------------------------------*/

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'reference_order_id', 'order_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'order_id', 'order_id');
    }

    /* -------------------------------------------------
     |  Scopes
     |-------------------------------------------------*/

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /* -------------------------------------------------
     |  Helpers
     |-------------------------------------------------*/

    public function isGuest(): bool
    {
        return is_null($this->customer_id);
    }

    public function getCustomerNameAttribute(): string
    {
        if ($this->customer) {
            return $this->customer->fullName();
        }
        return $this->guest_name ?? 'Guest';
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function isComplete(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Generate a unique reference code like "IM-00042".
     */
    public static function generateReferenceCode(): string
    {
        do {
            $code = 'IM-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('reference_code', $code)->exists());

        return $code;
    }
}