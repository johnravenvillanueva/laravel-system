<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = DemoData::products();
        if ($q = $request->query('q')) {
            $products = $products->filter(fn($p) => str_contains(strtolower($p->name), strtolower($q)));
        }
        if ($cat = $request->query('category')) {
            $products = $products->where('category_id', (int) $cat);
        }

        return view('admin.products.index', [
            'products' => $products->values(),
            'categories' => DemoData::categories(),
        ]);
    }

    public function create()
    {
        return view('admin.products.create', ['categories' => DemoData::categories()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required','string','max:100'],
            'description' => ['nullable','string'],
            'category_id' => ['required','integer'],
            'price' => ['required','numeric','min:0'],
            'is_available' => ['required','in:0,1'],
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(int $id)
    {
        $product = DemoData::products()->firstWhere('id', $id) ?? DemoData::products()->first();
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => DemoData::categories(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => ['required','string','max:100'],
            'description' => ['nullable','string'],
            'category_id' => ['required','integer'],
            'price' => ['required','numeric','min:0'],
            'is_available' => ['required','in:0,1'],
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(int $id)
    {
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
}