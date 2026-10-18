<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $query = Product::with('category');

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
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'description'  => ['nullable', 'string'],
            'category_id'  => ['required', 'exists:categories,category_id'],
            'price'        => ['required', 'numeric', 'min:0'],
            'is_available' => ['required', 'in:0,1'],
            'image'        => ['nullable', 'image', 'max:2048'], // max 2 MB
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = 'storage/' . $path;
        }
        unset($data['image']);

        Product::create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(int $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'description'  => ['nullable', 'string'],
            'category_id'  => ['required', 'exists:categories,category_id'],
            'price'        => ['required', 'numeric', 'min:0'],
            'is_available' => ['required', 'in:0,1'],
            'image'        => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            // Delete old file if it exists
            if ($product->image_url && Storage::disk('public')->exists(str_replace('storage/', '', $product->image_url))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $product->image_url));
            }

            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = 'storage/' . $path;
        }
        unset($data['image']);

        $product->update($data);

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
}