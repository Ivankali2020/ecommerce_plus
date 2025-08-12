<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product as ProductModel;
use App\Traits\WithCart;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Product extends Component
{
    use WithCart, WithPagination;

    // public $storeID;

    public $brand_id;

    public $category_id;

    public int $perPage = 10;

    public bool $hasMorePages = true;

    public ?Collection $products;

    public $selectedCategory = null;

    // protected $listeners = ['refreshProducts' => '$refresh'];

    public function __construct()
    {
        $this->products = new Collection;
    }

    public function mount()
    {
        // Initialize empty collection
        try {

            $this->getProductData();
            $this->selectedCategory = null;
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation error
            Notification::make()
                ->title('Invalid store ID')
                ->body('The store ID provided is invalid')
                ->danger()
                ->send();
            session()->flash('error', 'Store not exists');
        }
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->withCount([
                'products' => function ($query) {
                    $query->where('tenant_id', tenant('id'))
                        ->whereNotIn('id', $this->recommendedProducts->pluck('id'))
                        ->where('is_public', true);
                },
            ])->latest()->get()->where('products_count', '>', 0);
    }

    public function filterByCategory($categoryId)
    {
        // Reset the state
        $this->selectedCategory = $categoryId;
        $this->perPage = 10;
        $this->hasMorePages = true;
        $this->resetPage(); // Reset pagination state

        // Initialize new collection
        $this->products = new Collection;

        // Get fresh data
        $this->getProductData();
    }

    public function getProductData()
    {
        try {
            $query = ProductModel::query()
                ->whereNotIn('id', $this->recommendedProducts->pluck('id'))
                ->where('is_public', true)
                ->when($this->selectedCategory, function ($query) {
                    return $query->where('category_id', $this->selectedCategory);
                })
                ->with(['category', 'brand', 'variants'])
                ->latest();

            // Get total count for current query
            $total = $query->count();

            // Calculate if we have more pages
            $this->hasMorePages = $total >= $this->perPage;

            // Get paginated results
            $newProducts = $query->skip($this->perPage - 10)->take($this->perPage)->get();

            $this->products = $this->products->merge($newProducts)->unique('id');
        } catch (\Exception $e) {
            $this->products = new Collection;
            $this->hasMorePages = false;
            logger()->error('Error loading products: ' . $e->getMessage());
        }
    }

    public function loadMore(): void
    {
        if (! $this->hasMorePages) {
            return;
        }

        try {

            $this->perPage += 10;
            $this->getProductData();
        } catch (\Exception $e) {
            logger()->error('Error loading more products: ' . $e->getMessage());
            $this->hasMorePages = false;
        }
    }

    #[Computed]
    public function recommendedProducts()
    {
        return ProductModel::where('tenant_id', tenant('id'))
            ->where('is_recommended', true)
            ->where('is_public', true)
            ->with(['category', 'brand', 'variants'])->latest()
            ->take(5)->get();
    }

    public function render()
    {
        return view('livewire.product');
    }
}
