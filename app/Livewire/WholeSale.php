<?php

namespace App\Livewire;

use App\Helper\UserHelper;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use App\Traits\WithCart;
use Livewire\Component;
use Livewire\WithPagination;

class WholeSale extends Component
{
    use WithCart, WithPagination;

    public $search = '';
    public $selectedCategory = null;
    public $selectedBrand = null;
    public $minPrice = null;
    public $maxPrice = null;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;
    public $seller_id;
    public $showVariants = true;
    public $availableOnly = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => null],
        'selectedBrand' => ['except' => null],
        'minPrice' => ['except' => null],
        'maxPrice' => ['except' => null],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedMinPrice()
    {
        $this->resetPage();
    }

    public function updatedMaxPrice()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatedSelectedBrand()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->seller_id = UserHelper::getUserSellerId();
    }

    public function setCategory($categoryId)
    {
        $this->selectedCategory = $categoryId === $this->selectedCategory ? null : $categoryId;
        $this->resetPage();
    }

    public function setBrand($brandId)
    {
        $this->selectedBrand = $brandId === $this->selectedBrand ? null : $brandId;
        $this->resetPage();
    }

    public function setSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearPriceFilter()
    {
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->resetPage();
    }

    public function clearAllFilters()
    {
        $this->search = '';
        $this->selectedCategory = null;
        $this->selectedBrand = null;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->availableOnly = false;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function toggleAvailableOnly()
    {
        $this->availableOnly = !$this->availableOnly;
        $this->resetPage();
    }

    public function toggleShowVariants()
    {
        $this->showVariants = !$this->showVariants;
        $this->resetPage();
    }

    public function loadMore()
    {
        $this->perPage += 15;
    }

    public function render()
    {
        $categories = Category::where('seller_id', $this->seller_id)
            ->withCount(['products' => function ($query) {
                $query->where('is_whole_sale', true)
                    ->where('is_public', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();

        $brands = Brand::where('seller_id', $this->seller_id)
            ->withCount(['products' => function ($query) {
                $query->where('is_whole_sale', true)
                    ->where('is_public', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();

        // Query for wholesale products
        $productsQuery = Product::query()
            ->where('seller_id', $this->seller_id)
            ->where('is_public', true)
            ->where('is_whole_sale', true)
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%')
                        ;
                });
            })
            ->when($this->selectedCategory, function ($query) {
                return $query->where('category_id', $this->selectedCategory);
            })
            ->when($this->selectedBrand, function ($query) {
                return $query->where('brand_id', $this->selectedBrand);
            })
            ->when($this->minPrice, function ($query) {
                return $query->where('price', '>=', $this->minPrice);
            })
            ->when($this->maxPrice, function ($query) {
                return $query->where('price', '<=', $this->maxPrice);
            })
            ->when($this->availableOnly, function ($query) {
                return $query->where('quantity', '>', 0);
            })
            ->with(['category', 'brand', 'variants' => function ($query) {
                if ($this->availableOnly) {
                    $query->where('quantity', '>', 0);
                }
            }])
            ->orderBy($this->sortBy, $this->sortDirection);

        $products = $productsQuery->take($this->perPage)->get();
        $hasMorePages = $productsQuery->count() > $this->perPage;

        // Get wholesale variants if show variants is enabled
        $variants = collect();
        if ($this->showVariants) {
            $variantsQuery = Variant::query()
                ->whereHas('product', function ($query) {
                    $query->where('seller_id', $this->seller_id)
                        ->where('is_public', true);
                })
                ->where('is_whole_sale', true)
                ->when($this->search, function ($query) {
                    return $query->where(function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('sku', 'like', '%' . $this->search . '%')
                            ->orWhereHas('product', function ($productQuery) {
                                $productQuery->where('name', 'like', '%' . $this->search . '%')
                                    ->orWhere('description', 'like', '%' . $this->search . '%');
                            });
                    });
                })
                ->when($this->selectedCategory, function ($query) {
                    return $query->whereHas('product', function ($productQuery) {
                        $productQuery->where('category_id', $this->selectedCategory);
                    });
                })
                ->when($this->selectedBrand, function ($query) {
                    return $query->whereHas('product', function ($productQuery) {
                        $productQuery->where('brand_id', $this->selectedBrand);
                    });
                })
                ->when($this->minPrice, function ($query) {
                    return $query->where('price', '>=', $this->minPrice);
                })
                ->when($this->maxPrice, function ($query) {
                    return $query->where('price', '<=', $this->maxPrice);
                })
                ->when($this->availableOnly, function ($query) {
                    return $query->where('quantity', '>', 0);
                })
                ->with(['product.category', 'product.brand'])
                ->orderBy($this->sortBy === 'created_at' ? 'created_at' : 'name', $this->sortDirection);

            $variants = $variantsQuery->take($this->perPage)->get();
        }


        return view('livewire.whole-sale', [
            'products' => $products,
            'variants' => $variants,
            'categories' => $categories,
            'brands' => $brands,
            'hasMorePages' => $hasMorePages,
            'totalProducts' => $productsQuery->count(),
            'totalVariants' => $this->showVariants ? $variantsQuery->count() : 0,
        ]);
    }
}
