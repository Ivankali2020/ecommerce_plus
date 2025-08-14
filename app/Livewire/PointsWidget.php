<?php

namespace App\Livewire;

use App\Services\PointService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PointsWidget extends Component
{
    public $showHistory = false;

    protected $pointService;
    protected $listeners = ['user-points-updated' => '$refresh'];

    public function boot(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

    public function toggleHistory()
    {
        $this->showHistory = !$this->showHistory;
    }

    public function render()
    {
        $user = Auth::user();
        $pointsSummary = null;
        $recentTransactions = collect();

        if ($user) {
            $pointsSummary = $this->pointService->getPointsSummary($user);
            if ($this->showHistory) {
                $recentTransactions = $this->pointService->getTransactionHistory($user, 10);
            }
        }

        return view('livewire.points-widget', [
            'user' => $user,
            'pointsSummary' => $pointsSummary,
            'recentTransactions' => $recentTransactions
        ]);
    }
}
