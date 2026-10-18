<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        [$from, $to, $range] = $this->resolveRange($request);

        // Base filtered query
        $salesQuery = Sale::with(['staff', 'customer'])
            ->whereBetween('sale_date', [$from, $to]);

        $sales = $salesQuery->clone()->latest('sale_date')->get();

        // Summary numbers
        $total = $sales->sum('total_amount');
        $count = $sales->count();
        $avg   = $count > 0 ? $total / $count : 0;

        // Chart data — daily totals within the range
        $chart = Sale::select(
                DB::raw('DATE(sale_date) as day'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('sale_date', [$from, $to])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        // Fill in missing days with 0 so the chart doesn't skip dates
        $chartLabels = [];
        $chartData = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $chartLabels[] = $cursor->format('M d');
            $chartData[]   = (float) ($chart[$key] ?? 0);
            $cursor->addDay();
        }

        return view('admin.reports.sales', [
            'sales'       => $sales,
            'total'       => $total,
            'count'       => $count,
            'avg'         => $avg,
            'chartLabels' => $chartLabels,
            'chartData'   => $chartData,
            'from'        => $from,
            'to'          => $to,
            'range'       => $range,
        ]);
    }

    public function inventory(Request $request)
    {
        $query = InventoryItem::query();

        if ($q = $request->query('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('category', 'like', "%$q%");
            });
        }

        $items = $query->orderBy('category')->orderBy('name')->get();

        $totalItems = $items->count();
        $lowCount   = $items->filter(fn($i) => $i->isLowStock())->count();

        // Rough valuation — just a demo. Real value = cost_per_unit × quantity.
        // We don't store cost per item yet, so we use a placeholder multiplier.
        $totalValue = $items->sum(function ($i) {
            $placeholderUnitCost = match (strtolower($i->unit)) {
                'kg'  => 600.00,
                'l'   => 120.00,
                'pcs' => 5.00,
                default => 50.00,
            };
            return (float) $i->quantity * $placeholderUnitCost;
        });

        return view('admin.reports.inventory', [
            'items'      => $items,
            'totalItems' => $totalItems,
            'lowCount'   => $lowCount,
            'totalValue' => $totalValue,
        ]);
    }

    protected function resolveRange(Request $request): array
    {
        $range = $request->query('range', 'week');

        switch ($range) {
            case 'day':
                $from = now()->startOfDay();
                $to   = now()->endOfDay();
                break;

            case 'month':
                $from = now()->startOfMonth();
                $to   = now()->endOfMonth();
                break;

            case 'custom':
                $from = $request->query('from')
                    ? Carbon::parse($request->query('from'))->startOfDay()
                    : now()->subDays(7)->startOfDay();
                $to = $request->query('to')
                    ? Carbon::parse($request->query('to'))->endOfDay()
                    : now()->endOfDay();
                break;

            case 'week':
            default:
                $from = now()->startOfWeek();
                $to   = now()->endOfWeek();
                break;
        }

        return [$from, $to, $range];
    }
}