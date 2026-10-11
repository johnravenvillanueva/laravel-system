<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData;

class ReportController extends Controller
{
    public function sales()
    {
        $sales = DemoData::sales();

        return view('admin.reports.sales', [
            'sales'       => $sales,
            'total'       => $sales->sum('total_amount'),
            'count'       => $sales->count(),
            'avg'         => $sales->avg('total_amount') ?? 0,
            'chartLabels' => ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            'chartData'   => [1200, 1800, 1500, 2200, 2800, 3400, 3100],
        ]);
    }

    public function inventory()
    {
        $items = DemoData::inventory();

        return view('admin.reports.inventory', [
            'items'      => $items,
            'totalItems' => $items->count(),
            'lowCount'   => $items->filter(fn($i) => $i->quantity <= $i->min_threshold)->count(),
            'totalValue' => $items->sum(fn($i) => $i->quantity * 100), // fake value
        ]);
    }
}