<?php

namespace App\Http\Controllers;

use App\Support\DemoData;
use Illuminate\Http\Request;

class MenuController extends Controller
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

        return view('customer.menu', [
            'products' => $products->values(),
            'categories' => DemoData::categories(),
        ]);
    }

    public function show(int $id)
    {
        $product = DemoData::products()->firstWhere('id', $id) ?? DemoData::products()->first();

        return view('customer.product-show', ['product' => $product]);
    }
}