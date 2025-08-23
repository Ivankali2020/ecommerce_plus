<?php

namespace App\Livewire;

use App\Helper\UserHelper;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use App\Traits\WithCart;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
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

    public $sortBy = null;

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
        $this->availableOnly = ! $this->availableOnly;
        $this->resetPage();
    }

    public function toggleShowVariants()
    {
        $this->showVariants = ! $this->showVariants;
        $this->resetPage();
    }

    public function loadMore()
    {
        $this->perPage += 15;
    }

    #[Computed]
    public function categories()
    {
        $cacheKey = "wholesale_categories_{$this->seller_id}";

        return Cache::remember($cacheKey, 1800, function () {
            return Category::where('seller_id', $this->seller_id)
                ->withCount(['products' => function ($query) {
                    $query->where('is_whole_sale', true)
                        ->where('is_public', true);
                }])
                ->having('products_count', '>', 0)
                ->orderBy('name')
                ->get();
        });
    }

    #[Computed]
    public function brands()
    {
        $cacheKey = "wholesale_brands_{$this->seller_id}";

        return Cache::remember($cacheKey, 1800, function () {
            return Brand::where('seller_id', $this->seller_id)
                ->withCount(['products' => function ($query) {
                    $query->where('is_whole_sale', true)
                        ->where('is_public', true);
                }])
                ->having('products_count', '>', 0)
                ->orderBy('name')
                ->get();
        });
    }

    #[Computed]
    public function products()
    {
        $query = Product::select(['id', 'name', 'description', 'sku', 'price', 'quantity', 'category_id', 'brand_id', 'seller_id', 'is_public', 'is_whole_sale', 'created_at'])
            ->where('seller_id', $this->seller_id)
            ->where('is_public', true)
            ->where('is_whole_sale', true)
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%');
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
            ->with([
                'category:id,name',
                'brand:id,name',
                'variants' => function ($query) {
                    $query->select(['id', 'product_id', 'name', 'sku', 'price', 'quantity', 'is_whole_sale']);
                    if ($this->availableOnly) {
                        $query->where('quantity', '>', 0);
                    }
                },
                'media',
            ])
            ->orderBy($this->sortBy === null ? 'created_at' : 'name', $this->sortDirection);

        return $query->take($this->perPage)->get();
    }

    #[Computed]
    public function variants()
    {
        if (! $this->showVariants) {
            return collect();
        }

        $query = Variant::select(['id', 'product_id', 'name', 'sku', 'price', 'quantity', 'is_whole_sale', 'created_at'])
            ->whereHas('product', function ($query) {
                $query->where('seller_id', $this->seller_id)
                    ->where('is_public', true);
            })
            ->where('is_whole_sale', true)
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%')
                        ->orWhereHas('product', function ($productQuery) {
                            $productQuery->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('description', 'like', '%'.$this->search.'%');
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
            ->with([
                'product:id,name,description,category_id,brand_id',
                'product.category:id,name',
                'product.brand:id,name',
                'media',
            ])
            ->orderBy($this->sortBy === null ? 'created_at' : 'name', $this->sortDirection);

        return $query->take($this->perPage)->get();
    }

    #[Computed]
    public function hasMoreProducts()
    {
        $query = Product::where('seller_id', $this->seller_id)
            ->where('is_public', true)
            ->where('is_whole_sale', true)
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%');
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
            });

        return $query->count() > $this->perPage;
    }

    #[Computed]
    public function totalProducts()
    {
        $cacheKey = "wholesale_total_products_{$this->seller_id}_".md5(serialize([
            $this->search,
            $this->selectedCategory,
            $this->selectedBrand,
            $this->minPrice,
            $this->maxPrice,
            $this->availableOnly,
        ]));

        return Cache::remember($cacheKey, 300, function () {
            return Product::where('seller_id', $this->seller_id)
                ->where('is_public', true)
                ->where('is_whole_sale', true)
                ->when($this->search, function ($query) {
                    return $query->where(function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('description', 'like', '%'.$this->search.'%')
                            ->orWhere('sku', 'like', '%'.$this->search.'%');
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
                ->count();
        });
    }

    #[Computed]
    public function totalVariants()
    {
        if (! $this->showVariants) {
            return 0;
        }

        $cacheKey = "wholesale_total_variants_{$this->seller_id}_".md5(serialize([
            $this->search,
            $this->selectedCategory,
            $this->selectedBrand,
            $this->minPrice,
            $this->maxPrice,
            $this->availableOnly,
        ]));

        return Cache::remember($cacheKey, 300, function () {
            return Variant::whereHas('product', function ($query) {
                $query->where('seller_id', $this->seller_id)
                    ->where('is_public', true);
            })
                ->where('is_whole_sale', true)
                ->when($this->search, function ($query) {
                    return $query->where(function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('sku', 'like', '%'.$this->search.'%')
                            ->orWhereHas('product', function ($productQuery) {
                                $productQuery->where('name', 'like', '%'.$this->search.'%')
                                    ->orWhere('description', 'like', '%'.$this->search.'%');
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
                ->count();
        });
    }

    public function render()
    {
        return view('livewire.whole-sale', [
            'products' => $this->products,
            'variants' => $this->variants,
            'categories' => $this->categories,
            'brands' => $this->brands,
            'hasMorePages' => $this->hasMoreProducts,
            'totalProducts' => $this->totalProducts,
            'totalVariants' => $this->totalVariants,
        ]);
    }
}
