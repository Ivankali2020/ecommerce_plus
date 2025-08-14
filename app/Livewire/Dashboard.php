<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard', [
            'totalOrders' => Order::where('user_id', Auth::id())->count(),
            'recentOrders' => Order::where('user_id', Auth::id())->with(['seller:id,name'])->latest()->take(5)->get(),
            'chartLabels' => $this->getLastSevenDays(),
            'chartData' => $this->getSalesData(),
        ]);
    }

    private function getLastSevenDays()
    {
        return collect(range(6, 0))->map(function ($days) {
            return now()->subDays($days)->format('M d');
        });
    }

    private function getSalesData()
    {
        return Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as total'))
            ->where('user_id', Auth::id())
            ->whereBetween('created_at', [now()->subDays(6), now()])
            ->groupBy('date')
            ->get()
            ->pluck('total');
    }
}
