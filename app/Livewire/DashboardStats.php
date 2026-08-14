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

        return view('livewire.dashboard-stats', compact(
            'totalMemos',
            'archivedMemos'
        ));
    }
}