<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Http\Request;
use Livewire\Component;

class Invoice extends Component
{
    public Order $record;

    public function mount(Request $request,$record)
    {
        // dd($record);
        // $this->record = Order::findOrFail($request->record);
        $this->record = $record;
    }

    public function render()
    {
        return view('livewire.invoice');
    }
}
