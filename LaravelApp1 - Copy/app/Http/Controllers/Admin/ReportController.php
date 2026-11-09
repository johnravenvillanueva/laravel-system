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

        $items = (clone $query)->orderBy('category')->orderBy('name')->get();
        $summary = $query
            ->selectRaw('COUNT(*) as total_items')
            ->selectRaw('SUM(CASE WHEN quantity <= min_threshold THEN 1 ELSE 0 END) as low_count')
            ->selectRaw('SUM(CASE WHEN quantity <= 0 THEN 1 ELSE 0 END) as out_count')
            ->selectRaw('SUM(CASE WHEN unit_cost IS NULL THEN 1 ELSE 0 END) as uncosted_count')
            ->selectRaw('SUM(CASE WHEN unit_cost IS NOT NULL THEN quantity * unit_cost ELSE 0 END) as total_value')
            ->first();

        return view('admin.reports.inventory', [
            'items'           => $items,
            'totalItems'      => $summary->total_items ?? 0,
            'lowCount'        => $summary->low_count ?? 0,
            'outOfStockCount' => $summary->out_count ?? 0,
            'uncostedCount'   => $summary->uncosted_count ?? 0,
            'totalValue'      => $summary->total_value ?? 0,
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