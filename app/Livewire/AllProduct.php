<?php

namespace App\Livewire;

use App\Helper\UserHelper;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Traits\WithCart;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class AllProduct extends Component
{
    use WithCart, WithPagination;

    public $search = '';

    public $selectedCategory = null;

    public $selectedBrand = null;

    public $perPage = 15;

    public $seller_id;

    public $discountOnly = false;

    public $filterType = 'category';

    public bool $hasMorePages = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => null],
        'selectedBrand' => ['except' => null],
        'filterType' => ['except' => 'category'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
        $this->clearProductsCache();
    }

    public function setCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
        $this->clearProductsCache();
    }

    public function loadMore()
    {
        $this->perPage += 15;
        $this->clearProductsCache();
    }

    public function toggleDiscountFilter()
    {
        $this->discountOnly = ! $this->discountOnly;
        $this->resetPage();
        $this->clearProductsCache();
    }

    public function mount()
    {
        $this->seller_id = UserHelper::getUserSellerId();
    }

    public function setFilterType($type)
    {
        $this->filterType = $type;
        $this->selectedCategory = null;
        $this->selectedBrand = null;
        $this->resetPage();
        $this->clearProductsCache();
    }

    public function setBrand($brandId)
    {
        $this->selectedBrand = $brandId;
        $this->resetPage();
        $this->clearProductsCache();
    }

    protected function clearProductsCache(): void
    {
        // Clear all product caches for this seller when filters change
        $patterns = [
            "products_seller_{$this->seller_id}_*",
        ];

        foreach ($patterns as $pattern) {
            $keys = Cache::get($pattern, []);
            if (! empty($keys)) {
                Cache::forget($pattern);
            }
        }
    }

    #[Computed]
    public function categories()
    {
        return Cache::remember("categories_seller_{$this->seller_id}", 1800, function () {
            return Category::where('seller_id', $this->seller_id)->get();
        });
    }

    #[Computed]
    public function brands()
    {
        return Cache::remember("brands_seller_{$this->seller_id}", 1800, function () {
            return Brand::where('seller_id', $this->seller_id)->get();
        });
    }

    #[Computed]
    public function products()
    {
        $cacheKey = "products_seller_{$this->seller_id}_".md5(serialize([
            'search' => $this->search,
            'category' => $this->selectedCategory,
            'brand' => $this->selectedBrand,
            'discount' => $this->discountOnly,
            'perPage' => $this->perPage,
        ]));

        return Cache::remember($cacheKey, 300, function () {
            $query = Product::query()
                ->select(['id', 'name', 'category_id', 'brand_id', 'seller_id', 'mmk_price', 'mmk_discount_price', 'discount', 'quantity', 'MOQ', 'is_public', 'has_variant', 'created_at'])
                ->where('seller_id', $this->seller_id)
                ->where('is_public', true)
                ->when($this->search, function ($query) {
                    return $query->where('name', 'like', '%'.$this->search.'%');
                })
                ->when($this->selectedCategory, function ($query) {
                    return $query->where('category_id', $this->selectedCategory);
                })
                ->when($this->selectedBrand, function ($query) {
                    return $query->where('brand_id', $this->selectedBrand);
                })
                ->when($this->discountOnly, function ($query) {
                    return $query->where('discount', '>', 0)->orWhereHas('variants', function ($variantQuery) {
                        $variantQuery->where('discount', '>', 0);
                    });
                })
                ->with([
                    'media',
                    'category:id,name',
                    'brand:id,name',
                    'variants' => function ($query) {
                        $query->select(['id', 'product_id', 'name', 'mmk_price', 'mmk_discount_price', 'discount'])
                            ->with('media')
                            ->orderBy('created_at')
                            ->limit(1);
                    },
                ])
                ->orderBy('created_at', 'desc');

            // Calculate if there are more pages
            $total = $query->count();
            $this->hasMorePages = $total > $this->perPage;

            return $query->take($this->perPage)->get();
        });
    }

    public function render()
    {
        return view('livewire.all-product', [
            'products' => $this->products,
            'categories' => $this->categories,
            'brands' => $this->brands,
            'hasMorePages' => $this->hasMorePages,
        ]);
    }
}
