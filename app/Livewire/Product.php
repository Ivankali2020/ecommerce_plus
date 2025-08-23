<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product as ProductModel;
use App\Traits\WithCart;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
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
            // Cache the initial data
            $this->selectedCategory = null;
            $this->getProductData();
        } catch (ValidationException $e) {
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
        // Get IDs to exclude (recommended and wholesale products) - optimized to avoid loading collections
        $excludeIds = $this->recommendedProducts->pluck('id')->merge($this->wholesaleProducts->pluck('id'))->unique();

        // Get recommended product IDs directly from database
        // $recommendedIds = ProductModel::where('tenant_id', tenant('id'))
        //     ->where('is_recommended', true)
        //     ->where('is_whole_sale', 0)
        //     ->where('is_public', true)
        //     ->latest()
        //     ->pluck('id');

        // // Get wholesale product IDs directly from database
        // $wholesaleIds = ProductModel::where('tenant_id', tenant('id'))
        //     ->where('is_whole_sale', true)
        //     ->where('is_public', true)
        //     ->latest()
        //     ->pluck('id');

        // $excludeIds = $recommendedIds->merge($wholesaleIds)->unique();

        return Category::query()
            ->withCount([
                'products' => function ($query) use ($excludeIds) {
                    $query->where('tenant_id', tenant('id'))
                        ->whereNotIn('id', $excludeIds)
                        ->where('is_public', true)
                        ->where('is_whole_sale', false);
                },
            ])
            ->having('products_count', '>', 0)
            ->latest()
            ->get();
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
            // Get IDs to exclude more efficiently
            $excludeIds = $this->recommendedProducts->pluck('id')->merge($this->wholesaleProducts->pluck('id'))->unique();

            // if ($this->products->isEmpty()) {
            //     // Only get exclude IDs on first load
            //     $recommendedIds = ProductModel::where('tenant_id', tenant('id'))
            //         ->where('is_recommended', true)
            //         ->where('is_whole_sale', 0)
            //         ->where('is_public', true)
            //         ->latest()
            //         ->pluck('id');

            //     $wholesaleIds = ProductModel::where('tenant_id', tenant('id'))
            //         ->where('is_whole_sale', true)
            //         ->where('is_public', true)
            //         ->latest()
            //         ->pluck('id');

            //     $excludeIds = $recommendedIds->merge($wholesaleIds)->unique();
            // }

            $query = ProductModel::query()
                ->where('tenant_id', tenant('id'))
                ->select(['id', 'name', 'category_id', 'brand_id', 'seller_id', 'mmk_price', 'mmk_discount_price', 'discount', 'quantity', 'MOQ', 'is_public', 'is_whole_sale', 'has_variant', 'tenant_id', 'created_at'])
                ->when($excludeIds->isNotEmpty(), function ($query) use ($excludeIds) {
                    return $query->whereNotIn('id', $excludeIds);
                })
                ->where('is_public', true)
                // ->where('is_whole_sale', false)
                ->when($this->selectedCategory, function ($query) {
                    return $query->where('category_id', $this->selectedCategory);
                })
                ->with([
                    'category:id,name',
                    'brand:id,name',
                    'media', // Eager load media to prevent N+1 queries
                    'variants' => function ($query) {
                        $query->select(['id', 'product_id', 'name', 'mmk_price', 'mmk_discount_price', 'discount'])
                            ->with('media') // Also load media for variants
                            ->orderBy('created_at')
                            ->limit(1); // Only get first variant for display
                    },
                ])
                ->latest('id');

            // Optimize pagination calculation
            if ($this->products->isEmpty()) {
                $total = $query->count();
                $this->hasMorePages = $total > $this->perPage;
            }

            // Get paginated results more efficiently
            $skip = max(0, $this->perPage - 10);
            $take = min(10, $this->perPage - $this->products->count());

            if ($take > 0) {
                $newProducts = $query->skip($skip)->take($take)->get();
                $this->products = $this->products->merge($newProducts)->unique('id');

                // Update hasMorePages based on actual results
                if ($newProducts->count() < $take) {
                    $this->hasMorePages = false;
                }
            }
        } catch (Exception $e) {
            $this->products = new Collection;
            $this->hasMorePages = false;
            logger()->error('Error loading products: '.$e->getMessage());
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
        } catch (Exception $e) {
            logger()->error('Error loading more products: '.$e->getMessage());
            $this->hasMorePages = false;
        }
    }

    #[Computed]
    public function recommendedProducts()
    {

        return Cache::remember('recommended_products_'.tenant('id'), 300, function () {
            return ProductModel::select(['id', 'name', 'category_id', 'brand_id', 'seller_id', 'mmk_price', 'mmk_discount_price', 'discount', 'quantity', 'MOQ', 'is_public', 'is_whole_sale', 'has_variant', 'tenant_id', 'is_recommended'])
                ->where('tenant_id', tenant('id'))
                ->where('is_recommended', true)
                // ->where('is_whole_sale', 0)
                ->where('is_public', true)
                ->with([
                    'category:id,name',
                    'brand:id,name',
                    'media', // Eager load media
                    'variants' => function ($query) {
                        $query->select(['id', 'product_id', 'name', 'mmk_price', 'mmk_discount_price', 'discount'])
                            ->with('media') // Also load media for variants
                            ->orderBy('created_at')
                            ->limit(1);
                    },
                ])
                ->limit(5)
                ->latest('id', 'desc')
                ->get();
        });
    }

    #[Computed]
    public function wholesaleProducts()
    {
        return Cache::remember('wholesale_products_'.tenant('id'), 300, function () {
            return ProductModel::select(['id', 'name', 'category_id', 'brand_id', 'seller_id', 'mmk_price', 'mmk_discount_price', 'discount', 'quantity', 'MOQ', 'is_public', 'is_whole_sale', 'has_variant', 'tenant_id'])
                ->where('tenant_id', tenant('id'))
                ->where('is_whole_sale', true)
                ->where('is_public', true)
                ->with([
                    'category:id,name',
                    'brand:id,name',
                    'media', // Eager load media
                    'variants' => function ($query) {
                        $query->select(['id', 'product_id', 'name', 'mmk_price', 'mmk_discount_price', 'discount'])
                            ->with('media') // Also load media for variants
                            ->orderBy('created_at')
                            ->limit(1);
                    },
                ])
                ->latest('id')
                ->limit(5)
                ->get();
        });
    }

    public function render()
    {
        return view('livewire.product');
    }
}
