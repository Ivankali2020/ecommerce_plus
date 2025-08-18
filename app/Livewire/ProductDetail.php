<?php

namespace App\Livewire;

use App\Models\Product;
use App\Traits\WithCart;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProductDetail extends Component
{
    use WithCart;

    public Product $product;

    public string $type;

    public function mount($product, $type = 'detail')
    {
        $this->product = $product;
        $this->type = $type;
    }

    #[Computed]
    public function relatedProducts()
    {
        return Product::query()
            ->when(Auth::check(), function ($q) {
                return $q->where('seller_id', Auth::user()->seller_id);
            })
            ->where('is_public', true)
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->with(['variants', 'category'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.product-detail', [
            'product' => $this->product,
        ])->layout('components.layouts.app', [
            'seo' => [
                'title' => $this->product->name,
                'description' => strip_tags(substr($this->product->description, 0, 160)),
                'image' => $this->product->productImg ?? $this->product->getFirstMediaUrl('images'),
                'url' => route('product', $this->product),
                'type' => 'product',
                'price' => $this->product->has_variant
                    ? $this->product->variants->first()->mmk_price
                    : $this->product->mmk_price,
                'currency' => 'MMK',
            ],
        ]);
    }
}
