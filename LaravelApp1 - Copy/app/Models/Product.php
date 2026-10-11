<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image_url',
        'is_available',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'is_available' => 'boolean',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /* -------------------------------------------------
     |  Relationships
     |-------------------------------------------------*/

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id', 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'product_id', 'product_id');
    }

    /* -------------------------------------------------
     |  Scopes
     |-------------------------------------------------*/

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /* -------------------------------------------------
     |  Helpers
     |-------------------------------------------------*/

    public function formattedPrice(): string
    {
        return '₱' . number_format((float) $this->price, 2);
    }
}