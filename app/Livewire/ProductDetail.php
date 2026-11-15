<?php

namespace App\Livewire;

use App\Models\Product;
use App\Traits\WithCart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProductDetail extends Component
{
    use WithCart;

    public Product $product;

    public string $type;

    public function mount($product, $type = 'detail')
    {
        // Ensure we have the product with necessary relationships loaded
        if (is_numeric($product)) {
            $this->product = Product::with([
                'category:id,name',
                'brand:id,name',
                'media',
                'variants' => function ($query) {
                    $query->select(['id', 'product_id', 'name', 'mmk_price', 'mmk_discount_price', 'discount'])
                        ->with('media');
                },
            ])->findOrFail($product);
        } else {
            $this->product = $product;
        }

        $this->type = $type;
    }

    /**
     * All gallery images: every variant's images first, then the product's own.
     *
     * @return Collection<int, array{full: string, thumb: string}>
     */
    #[Computed]
    public function galleryImages(): Collection
    {
        $media = $this->product->has_variant
            ? $this->product->variants->flatMap(fn ($variant) => $variant->getMedia('images'))
            : collect();

        return $media
            ->merge($this->product->getMedia('images'))
            ->map(fn ($image): array => [
                'full' => $image->getUrl(),
                'thumb' => $image->getUrl('thumb'),
            ])
            ->values();
    }

    #[Computed]
    public function relatedProducts()
    {
        $cacheKey = "related_products_{$this->product->id}_{$this->product->category_id}";

        return Cache::remember($cacheKey, 1800, function () {
            return Product::query()
                ->select(['id', 'name', 'category_id', 'brand_id', 'seller_id', 'mmk_price', 'mmk_discount_price', 'discount', 'quantity', 'MOQ', 'is_public', 'has_variant', 'created_at'])
                ->when(Auth::check(), function ($q) {
                    return $q->where('seller_id', Auth::user()->seller_id);
                })
                ->where('is_public', true)
                ->where('category_id', $this->product->category_id)
                ->where('id', '!=', $this->product->id)
                ->with([
                    'category:id,name',
                    'brand:id,name',
                    'media',
                    'variants' => function ($query) {
                        $query->select(['id', 'product_id', 'name', 'mmk_price', 'mmk_discount_price', 'discount'])
                            ->with('media')
                            ->orderBy('created_at')
                            ->limit(1);
                    },
                ])
                ->latest()
                ->limit(5)
                ->get();
        });
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
                'price' => $this->product->variants->first()?->mmk_price ?? $this->product->mmk_price,
                'currency' => 'MMK',
            ],
        ]);
    }
}
