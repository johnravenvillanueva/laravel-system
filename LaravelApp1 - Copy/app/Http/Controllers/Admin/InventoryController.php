<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::query();

        if ($q = $request->query('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%')
                    ->orWhere('category', 'like', '%' . $q . '%');
            });
        }

        $items = $query->orderBy('name')->get();

        return view('admin.inventory.index', compact('items'));
    }

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'category'      => ['required', 'string', 'max:50'],
            'quantity'      => ['required', 'numeric', 'min:0'],
            'unit'          => ['required', 'string', 'max:20'],
            'min_threshold' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {
            $item = InventoryItem::create($data);

            // Log the initial stock-in
            if ($item->quantity > 0) {
                InventoryTransaction::create([
                    'inventory_id'     => $item->inventory_id,
                    'transaction_type' => 'add',
                    'quantity'         => $item->quantity,
                    'reference_type'   => 'initial',
                    'reference_id'     => null,
                    'performed_by'     => Auth::guard('admin')->id(),
                    'transaction_date' => now(),
                    'notes'            => 'Initial stock entry',
                ]);
            }
        });

        return redirect()
            ->route('admin.inventory.index')
            ->with('success', 'Inventory item created.');
    }

    public function edit(int $id)
    {
        $item = InventoryItem::findOrFail($id);
        return view('admin.inventory.create', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = InventoryItem::findOrFail($id);

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'category'      => ['required', 'string', 'max:50'],
            'quantity'      => ['required', 'numeric', 'min:0'],
            'unit'          => ['required', 'string', 'max:20'],
            'min_threshold' => ['required', 'numeric', 'min:0'],
        ]);

        $oldQty = (float) $item->quantity;
        $newQty = (float) $data['quantity'];

        DB::transaction(function () use ($item, $data, $oldQty, $newQty) {
            $item->update($data);

            // If quantity changed, log an adjustment
            if ($oldQty !== $newQty) {
                $diff = $newQty - $oldQty;
                InventoryTransaction::create([
                    'inventory_id'     => $item->inventory_id,
                    'transaction_type' => $diff > 0 ? 'add' : 'deduct',
                    'quantity'         => abs($diff),
                    'reference_type'   => 'adjustment',
                    'reference_id'     => null,
                    'performed_by'     => Auth::guard('admin')->id(),
                    'transaction_date' => now(),
                    'notes'            => 'Manual adjustment from ' . $oldQty . ' to ' . $newQty,
                ]);
            }
        });

        return redirect()
            ->route('admin.inventory.index')
            ->with('success', 'Inventory item updated.');
    }

    public function destroy(int $id)
    {
        $item = InventoryItem::findOrFail($id);
        $item->delete(); // cascade deletes its transactions

        return redirect()
            ->route('admin.inventory.index')
            ->with('success', 'Inventory item deleted.');
    }

    public function transactions()
    {
        $transactions = InventoryTransaction::with(['inventory', 'performedBy'])
            ->latest('transaction_date')
            ->paginate(50);

        return view('admin.inventory.transactions', compact('transactions'));
    }
}