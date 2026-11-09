<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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

        $items = $query->orderBy('name')->paginate(50)->withQueryString();
        $summary = InventoryItem::query()
            ->selectRaw('COUNT(*) as total_items')
            ->selectRaw('SUM(CASE WHEN quantity <= min_threshold THEN 1 ELSE 0 END) as low_count')
            ->selectRaw('SUM(CASE WHEN quantity <= 0 THEN 1 ELSE 0 END) as out_count')
            ->selectRaw('SUM(CASE WHEN unit_cost IS NULL THEN 1 ELSE 0 END) as uncosted_count')
            ->selectRaw('SUM(CASE WHEN unit_cost IS NOT NULL THEN quantity * unit_cost ELSE 0 END) as total_value')
            ->first();

        return view('admin.inventory.index', compact('items', 'summary'));
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
            'unit_cost'     => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
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
            'unit_cost'     => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
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

    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selected'      => ['required', 'array', 'min:1'],
            'selected.*'    => ['required', 'integer', 'distinct', 'exists:inventory_items,inventory_id'],
            'quantities'    => ['required', 'array'],
            'quantities.*'  => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'notes'         => ['required', 'string', 'max:1000'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $selected = $request->input('selected', []);
            $quantities = $request->input('quantities', []);

            if (! is_array($selected) || ! is_array($quantities)) {
                return;
            }

            foreach ($selected as $id) {
                if ((is_string($id) || is_int($id)) && ! array_key_exists((string) $id, $quantities)) {
                    $validator->errors()->add("quantities.$id", 'Enter an on-hand quantity for every selected item.');
                }
            }
        });

        $data = $validator->validate();
        $itemIds = array_map('intval', $data['selected']);
        $performedBy = Auth::guard('admin')->id();

        $updatedCount = DB::transaction(function () use ($itemIds, $data, $performedBy) {
            $items = InventoryItem::whereIn('inventory_id', $itemIds)
                ->orderBy('inventory_id')
                ->lockForUpdate()
                ->get();

            if ($items->count() !== count($itemIds)) {
                throw ValidationException::withMessages([
                    'selected' => 'One or more selected items are no longer available. Reload the inventory and try again.',
                ]);
            }

            $updatedCount = 0;
            foreach ($items as $item) {
                $newQuantity = (float) $data['quantities'][$item->inventory_id];
                $oldQuantity = (float) $item->quantity;

                if (number_format($oldQuantity, 2, '.', '') === number_format($newQuantity, 2, '.', '')) {
                    continue;
                }

                $difference = $newQuantity - $oldQuantity;
                $item->update(['quantity' => $newQuantity]);

                InventoryTransaction::create([
                    'inventory_id'     => $item->inventory_id,
                    'transaction_type' => $difference > 0 ? 'add' : 'deduct',
                    'quantity'         => abs($difference),
                    'reference_type'   => 'bulk_stocktake',
                    'performed_by'     => $performedBy,
                    'transaction_date' => now(),
                    'notes'            => 'Bulk stock count adjustment from '
                        . number_format($oldQuantity, 2, '.', '')
                        . ' to ' . number_format($newQuantity, 2, '.', '')
                        . '. Reason: ' . $data['notes'],
                ]);

                $updatedCount++;
            }

            return $updatedCount;
        });

        if ($updatedCount === 0) {
            return back()->with('info', 'No stock quantities changed.');
        }

        return back()->with('success', "Stock counts updated for {$updatedCount} item(s).");
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