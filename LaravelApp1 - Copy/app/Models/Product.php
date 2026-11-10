<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
     * The recipe: inventory items this product deducts per unit sold.
     * Managed from the admin Add/Edit Product forms.
     */
    public function ingredients()
    {
        return $this->hasMany(ProductIngredient::class, 'product_id', 'product_id');
    }

    public function isOrderable(int $quantity = 1): bool
    {
        if (! $this->is_available || $quantity < 1) {
            return false;
        }

        $this->loadMissing('ingredients.inventoryItem');

        foreach ($this->ingredients as $ingredient) {
            $inventoryItem = $ingredient->inventoryItem;
            $perProduct = (float) $ingredient->quantity;

            if (! $inventoryItem || $perProduct <= 0) {
                return false;
            }

            $reservations = DB::table('order_items')
                ->join('orders', 'orders.order_id', '=', 'order_items.order_id')
                ->join('product_ingredients', 'product_ingredients.product_id', '=', 'order_items.product_id')
                ->where('product_ingredients.inventory_id', $inventoryItem->inventory_id)
                ->whereIn('orders.status', ['pending', 'confirmed', 'preparing', 'ready'])
                ->select(
                    'order_items.quantity as order_quantity',
                    'product_ingredients.quantity as recipe_quantity'
                )
                ->when(DB::transactionLevel() > 0, fn ($query) => $query->lockForUpdate())
                ->get();
            $reserved = $reservations->sum(
                fn ($reservation) => (float) $reservation->order_quantity * (float) $reservation->recipe_quantity
            );

            $available = (float) $inventoryItem->quantity - $reserved;
            $required = $perProduct * $quantity;

            if ($required > $available + 0.00001) {
                return false;
            }
        }

        return true;
    }

    public static function lockInventoryForProducts(array $productIds): void
    {
        $inventoryIds = DB::table('product_ingredients')
            ->whereIn('product_id', $productIds)
            ->orderBy('inventory_id')
            ->pluck('inventory_id');

        if ($inventoryIds->isNotEmpty()) {
            InventoryItem::whereIn('inventory_id', $inventoryIds)
                ->orderBy('inventory_id')
                ->lockForUpdate()
                ->get();
        }
    }

    /**
     * Deduct stock for $qty units of this product, using the ingredients
     * the admin set on the product (what you see on the product form is
     * exactly what gets deducted).
     */
    public function deductInventory(int $qty, $referenceType, $referenceId, $staffId)
    {
        $this->loadMissing('ingredients.inventoryItem');

        foreach ($this->ingredients as $ingredient) {
            if (! $ingredient->inventoryItem) continue;

            $this->deductItem(
                $ingredient->inventoryItem->name,
                (float) $ingredient->quantity * $qty,
                $referenceType,
                $referenceId,
                $staffId
            );
        }
    }

    /**
     * The old hard-coded, name-based rules. No longer used when selling;
     * only used to seed starter recipes (migration + ProductIngredientSeeder).
     *
     * @return array<string,float> inventory item name => qty per 1 product
     */
    public static function legacyRecipe(string $productName): array
    {
        $name = strtolower($productName);
        $r = [];

        if (str_contains($name, 'macchiato') || str_contains($name, 'latte')
            || str_contains($name, 'americano') || str_contains($name, 'cappuccino') || str_contains($name, 'machiatos')) {
            $r['Coffee Beans (Arabica)'] = 0.02;
        }
        if (str_contains($name, 'latte') || str_contains($name, 'macchiato') || str_contains($name, 'cappuccino') || str_contains($name, 'machiatos')) {
            $r['Fresh Milk'] = 0.15;
        }
        if (str_contains($name, 'matcha') || str_contains($name, 'machatos')) {
            $r['Matcha Powder'] = 0.02;
        }
        if (str_contains($name, 'strawberry')) {
            $r['Strawberry Syrup'] = 0.03;
        }
        if (str_contains($name, 'choco') || str_contains($name, 'ube')) {
            $r['Chocolate Syrup'] = 0.03;
        }
        if (! str_contains($name, 'cookie')) {
            $r['Plastic Cups (16oz)'] = 1;
        }

        return $r;
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