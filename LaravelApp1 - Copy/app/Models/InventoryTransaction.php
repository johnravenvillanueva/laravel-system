<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $table = 'inventory_transactions';
    protected $primaryKey = 'transaction_id';

    public $timestamps = false;

    protected $fillable = [
        'inventory_id',
        'transaction_type',
        'quantity',
        'reference_type',
        'reference_id',
        'performed_by',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'quantity'         => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function inventory()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_id', 'inventory_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(Admin::class, 'performed_by', 'admin_id');
    }


    public function isAddition(): bool
    {
        return $this->transaction_type === 'add';
    }

    public function isDeduction(): bool
    {
        return $this->transaction_type === 'deduct';
    }
        public function getIdAttribute()
    {
        return $this->transaction_id;
    }
}