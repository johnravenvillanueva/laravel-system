<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menu list with optional search (q) and category filter.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->available();

        // Search by name
        if ($q = $request->query('q')) {
            $query->where('name', 'like', '%' . $q . '%');
        }

        // Filter by category id
        if ($categoryId = $request->query('category')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->get();
        $categories = Category::all();

        return view('customer.menu', compact('products', 'categories'));
    }

    /**
     * Single product detail.
     */
    public function show(int $id)
    {
        $product = Product::with('category')->findOrFail($id);

        // Related products from same category (excluding current)
        $related = Product::with('category')
            ->available()
            ->where('category_id', $product->category_id)
            ->where('product_id', '!=', $product->product_id)
            ->take(4)
            ->get();

        return view('customer.product-show', compact('product', 'related'));
    }
}