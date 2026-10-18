<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $table = 'inventory_items';
    protected $primaryKey = 'inventory_id';

    protected $fillable = [
        'name',
        'category',
        'quantity',
        'unit',
        'min_threshold',
    ];

    protected $casts = [
        'quantity'      => 'decimal:2',
        'min_threshold' => 'decimal:2',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'inventory_id', 'inventory_id');
    }

    public function isLowStock(): bool
    {
        return (float) $this->quantity <= (float) $this->min_threshold;
    }

    public function stockStatus(): string
    {
        return $this->isLowStock() ? 'low' : 'ok';
    }

    public function formattedQuantity(): string
    {
        return number_format((float) $this->quantity, 2) . ' ' . $this->unit;
    }
        public function getIdAttribute()
    {
        return $this->cart_id;
    }
}