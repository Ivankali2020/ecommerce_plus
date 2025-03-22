<?php

namespace App\Livewire\Seller;

use Livewire\Component;

class ProductDetailModal extends Component
{
    public $product = null;
    public $quantity = 1;
    public $selectedVariant = null;
    public $showModal = false;
    
    protected $listeners = ['openProductModal' => 'openModal'];
    
    public function render()
    {
        return view('livewire.seller.product-detail-modal');
    }
    
    public function openModal($product)
    {
        $this->product = $product;
        $this->quantity = 1;
        $this->selectedVariant = null;
        $this->showModal = true;
    }
    
    public function closeModal()
    {
        $this->showModal = false;
    }
    
    public function selectVariant($variantId)
    {
        $this->selectedVariant = collect($this->product['variants'])->firstWhere('id', $variantId);
    }
    
    public function addToCart()
    {
        $productToAdd = $this->product;
        
        if ($this->selectedVariant) {
            $productToAdd['price'] = $this->selectedVariant['price'];
        }
        
        $this->dispatch('productAdded', $productToAdd, $this->quantity, $this->selectedVariant ? $this->selectedVariant['id'] : null);
        $this->closeModal();
    }
}
