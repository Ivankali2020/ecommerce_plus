<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use App\Traits\WithCart;
use App\Helper\UserHelper;
use Livewire\WithPagination;

class AllProduct extends Component
{
    use WithPagination,WithCart;

    public $search = '';
    public $selectedCategory = null;
    public $perPage = 15;
    public $seller_id;
    public $discountOnly = false;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => null],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function setCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
    }

    public function loadMore()
    {
        $this->perPage += 15;
    }

    public function toggleDiscountFilter()
    {
        $this->discountOnly = !$this->discountOnly;
        $this->resetPage();
    }

    public function mount(){
        $this->seller_id = UserHelper::getUserSellerId();
    }

    public $filterType = 'category';
    public $selectedBrand = null;
        
    public function setFilterType($type)
    {
            $this->filterType = $type;
            $this->selectedCategory = null;
            $this->selectedBrand = null;
            $this->resetPage();
    }
        
    public function setBrand($brandId)
    {
            $this->selectedBrand = $brandId;
            $this->resetPage();
    }

    public function render()
    {
        $categories = Category::where('seller_id', $this->seller_id)->get();
        $brands = Brand::where('seller_id', $this->seller_id)->get();

        $productsQuery = Product::query()
            ->where('seller_id', $this->seller_id)
            ->where('is_public', true)
            ->when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedCategory, function ($query) {
                return $query->where('category_id', $this->selectedCategory);
            })
            ->when($this->selectedBrand, function ($query) {
                return $query->where('brand_id', $this->selectedBrand);
            })
            ->when($this->discountOnly, function ($query) {
                return $query->where('discount', '>', 0) ->orWhereHas('variants', function($variantQuery) {
                          $variantQuery->where('discount', '>', 0);
                      });
            })
            ->orderBy('created_at', 'desc');
        
        $products = $productsQuery->take($this->perPage)->get();
        $hasMorePages = $productsQuery->count() > $this->perPage;

        return view('livewire.all-product', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'hasMorePages' => $hasMorePages
        ]);
    }
}
