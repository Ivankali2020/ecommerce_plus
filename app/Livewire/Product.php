<?php

namespace App\Livewire;

use App\Helper\CheckFilamentSellerUser;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use App\Models\Product as ProductModel;

class Product extends Component
{
    public $storeID;

    // #[Reactive]
    // public $products;

    // #[Reactive]
    // public $recommendedProducts;

    protected $listeners = ['refreshProducts' => '$refresh'];

    public function mount($storeID)
    {
        Validator::make(
            ['storeID' => $storeID],
            ['storeID' => 'required|exists:sellers,id']
        )->validate();

        $this->storeID = $storeID;
    }

    #[Computed]
    public function products()
    {
        return ProductModel::where('seller_id', $this->storeID)
            ->where('is_public', true)
            ->with(['category', 'brand', 'variants'])->latest()
            ->get();
    }

    #[Computed]
    public function recommendedProducts()
    {
        return ProductModel::where('seller_id', $this->storeID)
            ->where('is_recommended', true)
            ->where('is_public', true)
            ->with(['category', 'brand', 'variants'])->latest()
            ->take(5)->get();
    }

    public function addToCart($productId)
    {
        try {
            // Your cart logic here

            $this->dispatch('notify', [
                'message' => 'Product added to cart successfully!',
                'type' => 'success'
            ]);

            Notification::make()->title('Product added to cart successfully!')->send();
            // Don't refresh the entire component
            $this->dispatch('cart-updated');

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Failed to add product to cart',
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.product');
    }
}
