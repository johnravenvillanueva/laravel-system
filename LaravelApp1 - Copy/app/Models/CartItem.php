<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';
    protected $primaryKey = 'cart_item_id';

    public $timestamps = false;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'special_instructions',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'added_at' => 'datetime',
    ];

    /* -------------------------------------------------
     |  Relationships
     |-------------------------------------------------*/

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /* -------------------------------------------------
     |  Helpers
     |-------------------------------------------------*/

    public function getSubtotalAttribute(): float
    {
        if (! $this->product) {
            return 0;
        }
        return (float) $this->product->price * (int) $this->quantity;
    }
}