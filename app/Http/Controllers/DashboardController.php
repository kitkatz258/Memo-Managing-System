<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;

class DashboardController extends Controller
{
    public function index()
    {
        $monthlyUploads = Memo::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = date('M', mktime(0, 0, 0, $i, 1));
            $data[] = $monthlyUploads[$i] ?? 0;
        }

        return view('dashboard', [
            'labels' => $labels,
            'data' => $data,
            'totalMemos' => Memo::count(),
            'archivedMemos' => Memo::onlyTrashed()->count(),
        ]);
    }
}
