<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $filters = $request->validate([
            'range' => 'sometimes|in:day,week,month,custom',
            'payment' => 'sometimes|nullable|in:cash,gcash',
            'type' => 'sometimes|nullable|in:walk-in,pre-order',
        ]);
        $range = $filters['range'] ?? 'week';

        if ($range === 'custom') {
            $dates = $request->validate([
                'from' => 'required|date_format:Y-m-d|before_or_equal:today',
                'to' => 'required|date_format:Y-m-d|after_or_equal:from|before_or_equal:today',
            ]);
        }

        [$from, $to] = $this->resolveRange($range, $dates ?? []);
        $periodDays = (int) $from->diffInDays($to) + 1;
        $previousFrom = $from->copy()->subDays($periodDays);
        $previousTo = $to->copy()->subDays($periodDays);
        $payment = $filters['payment'] ?? null;
        $type = $filters['type'] ?? null;

        $salesQuery = $this->filteredSales($from, $to, $payment, $type);
        if ($request->query('export') === 'csv') {
            $salesForExport = (clone $salesQuery)->with(['staff', 'customer'])->latest('sale_date')->get();

            return response()->streamDownload(function () use ($salesForExport) {
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Sale ID', 'Sale date', 'Customer', 'Staff', 'Payment method', 'Transaction type', 'Total (PHP)']);

                foreach ($salesForExport as $sale) {
                    fputcsv($output, [
                        $sale->sale_id,
                        $sale->sale_date->format('Y-m-d H:i:s'),
                        $this->safeCsvText(trim(($sale->customer?->first_name ?? '').' '.($sale->customer?->last_name ?? '')) ?: 'Walk-in'),
                        $this->safeCsvText(trim(($sale->staff?->first_name ?? '').' '.($sale->staff?->last_name ?? ''))),
                        ucfirst($sale->payment_method),
                        ucfirst($sale->transaction_type),
                        number_format((float) $sale->total_amount, 2, '.', ''),
                    ]);
                }

                fclose($output);
            }, 'sales-report-'.$from->toDateString().'-to-'.$to->toDateString().'.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $total = (float) (clone $salesQuery)->sum('total_amount');
        $count = (clone $salesQuery)->count();
        $previousTotal = (float) $this->filteredSales($previousFrom, $previousTo, $payment, $type)->sum('total_amount');
        $average = $count > 0 ? $total / $count : 0;
        $changePercent = $previousTotal > 0 ? (($total - $previousTotal) / $previousTotal) * 100 : null;

        $dailySales = (clone $salesQuery)
            ->selectRaw('DATE(sale_date) as sale_day')
            ->selectRaw('SUM(total_amount) as sales_total')
            ->groupBy('sale_day')
            ->orderBy('sale_day')
            ->get();

        [$chartLabels, $chartData] = $this->buildSalesChart($dailySales, $from, $to, $periodDays);

        $paymentBreakdown = (clone $salesQuery)
            ->select('payment_method')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(total_amount) as sales_total')
            ->groupBy('payment_method')
            ->orderBy('payment_method')
            ->get();

        $typeBreakdown = (clone $salesQuery)
            ->select('transaction_type')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(total_amount) as sales_total')
            ->groupBy('transaction_type')
            ->orderBy('transaction_type')
            ->get();

        $productPerformance = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
            ->join('products', 'sale_items.product_id', '=', 'products.product_id')
            ->whereBetween('sales.sale_date', [$from, $to])
            ->when($payment, fn ($query) => $query->where('sales.payment_method', $payment))
            ->when($type, fn ($query) => $query->where('sales.transaction_type', $type))
            ->select('products.name as product_name')
            ->selectRaw('SUM(sale_items.quantity) as units_sold')
            ->selectRaw('SUM(sale_items.subtotal) as sales_total')
            ->groupBy('sale_items.product_id', 'products.name')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        $sales = (clone $salesQuery)
            ->with(['customer', 'staff'])
            ->latest('sale_date')
            ->paginate(25)
            ->withQueryString();

        return view('admin.reports.sales', [
            'sales' => $sales,
            'total' => $total,
            'count' => $count,
            'average' => $average,
            'previousTotal' => $previousTotal,
            'changePercent' => $changePercent,
            'productPerformance' => $productPerformance,
            'paymentBreakdown' => $paymentBreakdown,
            'typeBreakdown' => $typeBreakdown,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'payment' => $payment,
            'type' => $type,
            'unitsSold' => (int) (clone $salesQuery)
                ->join('sale_items', 'sales.sale_id', '=', 'sale_items.sale_id')
                ->sum('sale_items.quantity'),
            'generatedAt' => now(),
            'generatedBy' => auth('admin')->user()->fullName(),
        ]);
    }

    public function inventory(Request $request)
    {
        $filters = $request->validate([
            'q' => 'sometimes|nullable|string|max:100',
            'status' => 'sometimes|nullable|in:low,out,ok',
        ]);

        $query = InventoryItem::query();
        $search = trim($filters['q'] ?? '');
        $status = $filters['status'] ?? null;

        if ($search !== '') {
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($status === 'out') {
            $query->where('quantity', '<=', 0);
        } elseif ($status === 'low') {
            $query->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'min_threshold');
        } elseif ($status === 'ok') {
            $query->whereColumn('quantity', '>', 'min_threshold');
        }

        if ($request->query('export') === 'csv') {
            $itemsForExport = (clone $query)->orderBy('category')->orderBy('name')->get();

            return response()->streamDownload(function () use ($itemsForExport) {
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Item', 'Category', 'Quantity', 'Unit', 'Unit cost (PHP)', 'Stock value (PHP)', 'Minimum threshold', 'Status']);

                foreach ($itemsForExport as $item) {
                    fputcsv($output, [
                        $this->safeCsvText($item->name),
                        $this->safeCsvText($item->category),
                        $item->quantity,
                        $this->safeCsvText($item->unit),
                        $item->unit_cost === null ? '' : number_format((float) $item->unit_cost, 2, '.', ''),
                        $item->unit_cost === null ? '' : number_format((float) $item->quantity * (float) $item->unit_cost, 2, '.', ''),
                        $item->min_threshold,
                        $this->inventoryStatus($item),
                    ]);
                }

                fclose($output);
            }, 'inventory-report-'.now()->toDateString().'.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $items = (clone $query)->orderBy('category')->orderBy('name')->get();
        $summary = (clone $query)
            ->selectRaw('COUNT(*) as total_items')
            ->selectRaw('SUM(CASE WHEN quantity <= min_threshold THEN 1 ELSE 0 END) as low_count')
            ->selectRaw('SUM(CASE WHEN quantity <= 0 THEN 1 ELSE 0 END) as out_count')
            ->selectRaw('SUM(CASE WHEN unit_cost IS NULL THEN 1 ELSE 0 END) as uncosted_count')
            ->selectRaw('SUM(CASE WHEN unit_cost IS NOT NULL THEN quantity * unit_cost ELSE 0 END) as total_value')
            ->first();

        $categoryBreakdown = (clone $query)
            ->select('category')
            ->selectRaw('COUNT(*) as item_count')
            ->selectRaw('SUM(CASE WHEN quantity <= min_threshold THEN 1 ELSE 0 END) as low_count')
            ->selectRaw('SUM(CASE WHEN quantity <= 0 THEN 1 ELSE 0 END) as out_count')
            ->selectRaw('SUM(CASE WHEN unit_cost IS NOT NULL THEN quantity * unit_cost ELSE 0 END) as known_value')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        return view('admin.reports.inventory', [
            'items' => $items,
            'totalItems' => $summary->total_items ?? 0,
            'lowCount' => $summary->low_count ?? 0,
            'outOfStockCount' => $summary->out_count ?? 0,
            'uncostedCount' => $summary->uncosted_count ?? 0,
            'totalValue' => $summary->total_value ?? 0,
            'categoryBreakdown' => $categoryBreakdown,
            'search' => $search,
            'status' => $status,
            'generatedAt' => now(),
            'generatedBy' => auth('admin')->user()->fullName(),
        ]);
    }

    protected function filteredSales(Carbon $from, Carbon $to, ?string $payment, ?string $type): Builder
    {
        return Sale::query()
            ->whereBetween('sale_date', [$from, $to])
            ->when($payment, fn ($query) => $query->where('payment_method', $payment))
            ->when($type, fn ($query) => $query->where('transaction_type', $type));
    }

    protected function resolveRange(string $range, array $dates): array
    {
        $today = now();

        switch ($range) {
            case 'day':
                $from = $today->copy()->startOfDay();
                $to = $today->copy()->endOfDay();
                break;
            case 'month':
                $from = $today->copy()->startOfMonth();
                $to = $today->copy()->endOfMonth();
                break;
            case 'custom':
                $from = Carbon::createFromFormat('Y-m-d', $dates['from'])->startOfDay();
                $to = Carbon::createFromFormat('Y-m-d', $dates['to'])->endOfDay();
                break;
            case 'week':
            default:
                $from = $today->copy()->startOfWeek();
                $to = $today->copy()->endOfWeek();
                break;
        }

        return [$from, $to];
    }

    protected function buildSalesChart($dailySales, Carbon $from, Carbon $to, int $periodDays): array
    {
        $granularity = $periodDays <= 31 ? 'day' : ($periodDays <= 180 ? 'week' : 'month');
        $totals = [];

        foreach ($dailySales as $row) {
            $date = Carbon::parse($row->sale_day);
            $bucket = match ($granularity) {
                'week' => $date->copy()->startOfWeek()->toDateString(),
                'month' => $date->copy()->startOfMonth()->toDateString(),
                default => $date->toDateString(),
            };
            $totals[$bucket] = ($totals[$bucket] ?? 0) + (float) $row->sales_total;
        }

        $cursor = match ($granularity) {
            'week' => $from->copy()->startOfWeek(),
            'month' => $from->copy()->startOfMonth(),
            default => $from->copy(),
        };
        $chartLabels = [];
        $chartData = [];

        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $chartLabels[] = $granularity === 'month' ? $cursor->format('M Y') : $cursor->format('M d');
            $chartData[] = (float) ($totals[$key] ?? 0);

            match ($granularity) {
                'week' => $cursor->addWeek(),
                'month' => $cursor->addMonth(),
                default => $cursor->addDay(),
            };
        }

        return [$chartLabels, $chartData];
    }

    protected function inventoryStatus(InventoryItem $item): string
    {
        if ((float) $item->quantity <= 0) {
            return 'Out of stock';
        }

        return $item->isLowStock() ? 'Low stock' : 'In stock';
    }

    protected function safeCsvText(?string $value): string
    {
        $value = $value ?? '';

        return preg_match('/^[\s]*[=+\-@]/', $value) ? "'".$value : $value;
    }
}
