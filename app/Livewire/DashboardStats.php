<?php

namespace App\Livewire;

use App\Models\Memo;
use Livewire\Component;

class DashboardStats extends Component
{
    protected $listeners = [
        'memo-saved' => 'refreshStats',
    ];

    public function refreshStats()
    {
        
    }

    public function render()
    {
        $totalMemos = Memo::count();
        $archivedMemos = Memo::onlyTrashed()->count();

        $year = now()->year;

        $uploads = Memo::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month');

        $labels = [];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = now()->setMonth($month)->format('M');
            $data[] = $uploads[$month] ?? 0;
        }

        $this->dispatch('chart-data-updated', labels: $labels, data: $data);

        return view('livewire.dashboard-stats', compact(
            'totalMemos',
            'archivedMemos',
            'labels',
            'data'
        ));
    }
}