<?php

namespace App\Livewire;

use App\Helper\UserHelper;
use App\Models\Product;
use App\Models\Seller;
use DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Store extends Component
{
    public $store_id;
    public Seller $seller;
    public function mount($id)
    {
        try{
            Validator::make(
                ['id' => $id],
                ['id' => 'required|exists:sellers,id']
            )->validate();

            $this->store_id = $id;

            // change seller id if they visit their website
            UserHelper::updateSellerId($id);
            $this->seller = Seller::find($id);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation error
            Notification::make()
                ->title('Invalid store ID')
                ->body('The store ID provided is invalid')
                ->danger()
                ->send();
            session()->flash('error', 'Store not exists or invalid in our databases');
        }
    }
    public function render()
    {
        return view('livewire.store');
    }
}
