<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'cart_id';

    protected $fillable = [
        'customer_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        return $this->hasMany(CartItem::class, 'cart_id', 'cart_id');
    }

    /* -------------------------------------------------
     |  Helpers
     |-------------------------------------------------*/

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return (float) $item->product->price * $item->quantity;
        });
    }

    public function getItemCountAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }
}