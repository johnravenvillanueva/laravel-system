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

    public function formattedPrice(): string
    {
        return '₱' . number_format((float) $this->price, 2);
    }
        /**
     * Alias for `$product->id` → `product_id`.
     */
    public function getIdAttribute()
    {
        return $this->product_id;
    }
        /**
     * Best-effort inventory deduction based on product name.
     * (A real system would use a `product_ingredients` pivot table).
     */
    public function deductInventory(int $qty, $referenceType, $referenceId, $staffId)
    {
        $name = strtolower($this->name);

        // 1. Deduct Beans for coffee drinks
        if (str_contains($name, 'macchiato') || str_contains($name, 'latte')
            || str_contains($name, 'americano') || str_contains($name, 'cappuccino') || str_contains($name, 'machiatos')) {
            $this->deductItem('Coffee Beans (Arabica)', 0.02 * $qty, $referenceType, $referenceId, $staffId);
        }

        // 2. Deduct Milk for milk-based drinks
        if (str_contains($name, 'latte') || str_contains($name, 'macchiato') || str_contains($name, 'cappuccino') || str_contains($name, 'machiatos')) {
            $this->deductItem('Fresh Milk', 0.15 * $qty, $referenceType, $referenceId, $staffId);
        }
        
        // 3. Deduct specific syrups/powders
        if (str_contains($name, 'matcha') || str_contains($name, 'machatos')) {
             $this->deductItem('Matcha Powder', 0.02 * $qty, $referenceType, $referenceId, $staffId);
        }
        if (str_contains($name, 'strawberry')) {
             $this->deductItem('Strawberry Syrup', 0.03 * $qty, $referenceType, $referenceId, $staffId);
        }
        if (str_contains($name, 'choco') || str_contains($name, 'ube')) {
             $this->deductItem('Chocolate Syrup', 0.03 * $qty, $referenceType, $referenceId, $staffId); // Just as an example
        }

        // 4. Deduct Cups for any drink (not pastry)
        if (! str_contains($name, 'cookie')) {
            $this->deductItem('Plastic Cups (16oz)', 1 * $qty, $referenceType, $referenceId, $staffId);
        }
    }

    protected function deductItem(string $itemName, float $qty, $referenceType, $referenceId, $staffId)
    {
        $item = \App\Models\InventoryItem::where('name', $itemName)->first();
        if (! $item) return;

        $newQty = max(0, (float) $item->quantity - $qty);
        $item->update(['quantity' => $newQty]);

        \App\Models\InventoryTransaction::create([
            'inventory_id'     => $item->inventory_id,
            'transaction_type' => 'deduct',
            'quantity'         => $qty,
            'reference_type'   => $referenceType,
            'reference_id'     => $referenceId,
            'performed_by'     => $staffId,
            'transaction_date' => now(),
            'notes'            => 'Auto-deducted from ' . $referenceType . ' #' . $referenceId,
        ]);
    }
}