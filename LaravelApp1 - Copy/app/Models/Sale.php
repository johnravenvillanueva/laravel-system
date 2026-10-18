<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'sale_id';

    protected $fillable = [
        'staff_id',
        'customer_id',
        'reference_order_id',
        'sale_date',
        'total_amount',
        'payment_method',
        'transaction_type',
    ];

    protected $casts = [
        'sale_date'    => 'datetime',
        'total_amount' => 'decimal:2',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];


    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'admin_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function referenceOrder()
    {
        return $this->belongsTo(Order::class, 'reference_order_id', 'order_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'sale_id');
    }


    public function scopeWalkIn($query)
    {
        return $query->where('transaction_type', 'walk-in');
    }

    public function scopePreOrder($query)
    {
        return $query->where('transaction_type', 'pre-order');
    }

    public function scopeInRange($query, $from, $to)
    {
        return $query->whereBetween('sale_date', [$from, $to]);
    }



    public function isWalkIn(): bool
    {
        return $this->transaction_type === 'walk-in';
    }

    public function isPreOrder(): bool
    {
        return $this->transaction_type === 'pre-order';
    }

    public function isCash(): bool
    {
        return $this->payment_method === 'cash';
    }

    public function isGCash(): bool
    {
        return $this->payment_method === 'gcash';
    }
        public function getIdAttribute()
    {
        return $this->sale_id;
    }
}