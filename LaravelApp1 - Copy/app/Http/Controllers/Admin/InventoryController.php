<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $items = DemoData::inventory();
        if ($q = $request->query('q')) {
            $items = $items->filter(fn($i) => str_contains(strtolower($i->name), strtolower($q)));
        }

        return view('admin.inventory.index', ['items' => $items->values()]);
    }

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required','string','max:100'],
            'category' => ['required','string','max:50'],
            'quantity' => ['required','numeric','min:0'],
            'unit' => ['required','string','max:20'],
            'min_threshold' => ['required','numeric','min:0'],
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item added.');
    }

    public function edit(int $id)
    {
        $item = DemoData::inventory()->firstWhere('id', $id) ?? DemoData::inventory()->first();
        return view('admin.inventory.create', ['item' => $item]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => ['required','string','max:100'],
            'category' => ['required','string','max:50'],
            'quantity' => ['required','numeric','min:0'],
            'unit' => ['required','string','max:20'],
            'min_threshold' => ['required','numeric','min:0'],
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory updated.');
    }

    public function destroy(int $id)
    {
        return redirect()->route('admin.inventory.index')->with('success', 'Item deleted.');
    }

    public function transactions()
    {
        return view('admin.inventory.transactions', ['transactions' => collect()]);
    }
}