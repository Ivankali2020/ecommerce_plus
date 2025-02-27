<?php

namespace App\Livewire;

use App\Helper\CheckFilamentSellerUser;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Product as ProductModel;
class Product extends Component
{

    protected $store_id;

    public function mount($storeID)
    {
        $this->store_id = $storeID;
    }

    #[Computed()]
    public function products()
    {
        return ProductModel::where('seller_id',$this->store_id)
                            ->where('is_public',true)
                            ->with(['category','brand','variants'])->latest()
                            ->get();
    }

    #[Computed()]
    public function recommendedProducts()
    {
        return ProductModel::where('seller_id',$this->store_id)
                            ->where('is_recommended',true)
                            ->where('is_public',true)
                            ->with(['category','brand','variants'])->latest()
                            ->take(5)->get();
    }

    public function render()
    {
        return view('livewire.product');
    }
}
