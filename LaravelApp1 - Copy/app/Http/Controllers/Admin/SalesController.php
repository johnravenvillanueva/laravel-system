<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.sales.index', ['sales' => DemoData::sales()]);
    }

    public function create()
    {
        return view('admin.sales.create', ['products' => DemoData::products()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => ['required','json'],
            'payment_method' => ['required','in:cash,gcash'],
            'notes' => ['nullable','string'],
        ]);

        // TODO: decode items, save sale, deduct inventory when DB is ready.

        return redirect()->route('admin.sales.index')->with('success', 'Sale recorded.');
    }

    public function show(int $id)
    {
        $sale = DemoData::sales()->firstWhere('id', $id) ?? DemoData::sales()->first();
        // Fake items for the detail view
        $sale->items = collect([
            (object)['quantity'=>2,'unit_price'=>145.00,'subtotal'=>290.00,'product'=>(object)['name'=>'Issang Macchiato']],
        ]);
        return view('admin.sales.show', ['sale' => $sale]);
    }
}