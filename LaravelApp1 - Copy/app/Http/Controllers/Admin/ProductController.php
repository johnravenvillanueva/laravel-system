<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $query = Product::with(['category', 'ingredients.inventoryItem']);

        if ($q = $request->query('q')) {
            $query->where('name', 'like', '%' . $q . '%');
        }

        if ($categoryId = $request->query('category')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $inventoryItems = InventoryItem::orderBy('name')->get();
        return view('admin.products.create', compact('categories', 'inventoryItems'));
    }

    public function store(Request $request)
    {
        $this->dropBlankIngredientRows($request);

        $data = $request->validate(array_merge([
            'name'         => ['required', 'string', 'max:100'],
            'description'  => ['nullable', 'string'],
            'category_id'  => ['required', 'exists:categories,category_id'],
            'price'        => ['required', 'numeric', 'min:0'],
            'is_available' => ['required', 'in:0,1'],
            'image'        => ['nullable', 'image', 'max:2048'], // max 2 MB
        ], $this->ingredientRules()), $this->ingredientMessages());

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = 'storage/' . $path;
        }
        unset($data['image']);

        $ingredients = $data['ingredients'] ?? [];
        unset($data['ingredients']);

        DB::transaction(function () use ($data, $ingredients) {
            $product = Product::create($data);
            $this->syncIngredients($product, $ingredients);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(int $id)
    {
        $product = Product::with('ingredients.inventoryItem')->findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $inventoryItems = InventoryItem::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories', 'inventoryItems'));
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $this->dropBlankIngredientRows($request);

        $data = $request->validate(array_merge([
            'name'         => ['required', 'string', 'max:100'],
            'description'  => ['nullable', 'string'],
            'category_id'  => ['required', 'exists:categories,category_id'],
            'price'        => ['required', 'numeric', 'min:0'],
            'is_available' => ['required', 'in:0,1'],
            'image'        => ['nullable', 'image', 'max:2048'],
        ], $this->ingredientRules()), $this->ingredientMessages());

        if ($request->hasFile('image')) {
            // Delete old file if it exists
            if ($product->image_url && Storage::disk('public')->exists(str_replace('storage/', '', $product->image_url))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $product->image_url));
            }

            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = 'storage/' . $path;
        }
        unset($data['image']);

        $ingredients = $data['ingredients'] ?? [];
        unset($data['ingredients']);

        DB::transaction(function () use ($product, $data, $ingredients) {
            $product->update($data);
            $this->syncIngredients($product, $ingredients);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);

        // Delete image file
        if ($product->image_url && Storage::disk('public')->exists(str_replace('storage/', '', $product->image_url))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $product->image_url));
        }

        // If product has been ordered/sold before, don't hard-delete (FK restrict)
        try {
            $product->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error',
                'Cannot delete this product — it has been used in past orders or sales. ' .
                'Mark it as unavailable instead.'
            );
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    /* -------------------------------------------------
     |  Ingredients (recipe) helpers
     |-------------------------------------------------*/

    /** Ignore completely empty ingredient rows submitted by the form. */
    protected function dropBlankIngredientRows(Request $request): void
    {
        $rows = collect($request->input('ingredients', []))
            ->filter(fn ($row) => is_array($row)
                && (filled($row['inventory_id'] ?? null) || filled($row['quantity'] ?? null)))
            ->values()
            ->all();

        $request->merge(['ingredients' => $rows]);
    }

    protected function ingredientRules(): array
    {
        return [
            'ingredients'                 => ['nullable', 'array'],
            'ingredients.*.inventory_id'  => ['required', 'exists:inventory_items,inventory_id', 'distinct'],
            'ingredients.*.quantity'      => ['required', 'numeric', 'gt:0'],
        ];
    }

    protected function ingredientMessages(): array
    {
        return [
            'ingredients.*.inventory_id.required' => 'Please choose an inventory item for each ingredient row.',
            'ingredients.*.inventory_id.distinct' => 'The same inventory item is listed more than once.',
            'ingredients.*.quantity.required'     => 'Please enter the amount used for each ingredient row.',
            'ingredients.*.quantity.gt'           => 'Ingredient amounts must be greater than 0.',
        ];
    }

    /** Replace the product's ingredient list with what was submitted. */
    protected function syncIngredients(Product $product, array $ingredients): void
    {
        $product->ingredients()->delete();

        foreach ($ingredients as $row) {
            $product->ingredients()->create([
                'inventory_id' => $row['inventory_id'],
                'quantity'     => $row['quantity'],
            ]);
        }
    }
}
