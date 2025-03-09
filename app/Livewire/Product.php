<?php

namespace App\Livewire;

use App\Helper\CheckFilamentSellerUser;
use App\Traits\WithCart;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use App\Models\Product as ProductModel;
use Livewire\WithPagination;
use App\Models\Category;
use Livewire\Attributes\On;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class Product extends Component
{
    use WithPagination, WithCart;

    public $storeID;
    public $brand_id;
    public $category_id;
    public int $perPage = 10;
    public bool $hasMorePages = true;
    public ?Collection $products;
    public $selectedCategory = null;


    protected $listeners = ['refreshProducts' => '$refresh'];

    public function __construct()
    {
        $this->products = new Collection();
    }

    public function mount($storeID)
    {
        // Initialize empty collection
        try {
            $validate = Validator::make(
                ['storeID' => $storeID],
                ['storeID' => 'required|exists:sellers,id']
            )->validate();

            $this->storeID = $storeID;
            // Load initial products
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
        return Category::withCount([
            'products' => function ($query) {
                $query->where('seller_id', $this->storeID)
                    ->whereNotIn('id', $this->recommendedProducts->pluck('id'))
                    ->where('is_public', true);
            }
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
        $this->products = new Collection();

        // Get fresh data
        $this->getProductData();
    }

    public function getProductData()
    {
        try {
            $query = ProductModel::whereNotIn('id', $this->recommendedProducts->pluck('id'))
                ->where('seller_id', $this->storeID)
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
            $this->products = new Collection();
            $this->hasMorePages = false;
            logger()->error('Error loading products: ' . $e->getMessage());
        }
    }

    public function loadMore(): void
    {
        if (!$this->hasMorePages) {
            return;
        }

        try {
            // $query = ProductModel::whereNotIn('id', $this->recommendedProducts->pluck('id'))
            //     ->where('seller_id', $this->storeID)
            //     ->where('is_public', true)
            //     ->when($this->selectedCategory, function ($query) {
            //         return $query->where('category_id', $this->selectedCategory);
            //     })
            //     ->with(['category', 'brand', 'variants'])
            //     ->latest();

            // // Get next batch of products
            // $nextProducts = $query->skip($this->perPage)
            //     ->take(10)
            //     ->get();

            // if ($nextProducts->isEmpty()) {
            //     $this->hasMorePages = false;
            //     return;
            // }

            // // Merge with existing products
            // $this->products = $this->products->merge($nextProducts)->unique('id');
            $this->perPage += 10;

            // // Check if there are more pages
            // $this->hasMorePages = $query->skip($this->perPage)->exists();
            $this->getProductData();

        } catch (\Exception $e) {
            logger()->error('Error loading more products: ' . $e->getMessage());
            $this->hasMorePages = false;
        }
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

    // #[On('add-to-cart')]
    // public function addToCart($productId, $variantId = null, $quantity = 1)
    // {
    //     try {
    //         if (!Auth::check()) {
    //             return redirect()->route('login');
    //         }

    //         // Check if $productId is an array containing all parameters
    //         if (is_array($productId) && isset($productId['productId'])) {
    //             $params = $productId;
    //             $productId = $params['productId'];
    //             $variantId = $params['variantId'];
    //             $quantity = $params['quantity'];
    //         }

    //         $product = ProductModel::where('id', $productId)
    //             ->with(['variants'])
    //             ->first();

    //         // Rest of your existing code...
    //         $variant = $variantId
    //             ? $product->variants->find($variantId)
    //             : ($product->has_variant ? $product->variants->first() : null);

    //         $price = $variant
    //             ? ($variant->discount > 0 ? $variant->mmk_discount_price : $variant->mmk_price)
    //             : ($product->discount > 0 ? $product->mmk_discount_price : $product->mmk_price);

    //         // Check existing cart item
    //         $existingCart = Cart::where([
    //             'user_id' => Auth::id(),
    //             'product_id' => $productId,
    //             'variant_id' => $variant?->id
    //         ])->first();

    //         $newQuantity = $existingCart ? $existingCart->quantity + 1 : $quantity;

    //         // Check MOQ
    //         if ($newQuantity < ( $variant?->MOQ ?? $product->MOQ)) {
    //             Notification::make()
    //                 ->title('Minimum Order Quantity not met')
    //                 ->body("Minimum order quantity is {$product->MOQ} pieces")
    //                 ->warning()
    //                 ->send();
    //             return;
    //         }


    //         // Update or create cart item
    //         Cart::updateOrCreate(
    //             [
    //                 'seller_id' => $this->storeID,
    //                 'user_id' => Auth::id(),
    //                 'product_id' => $productId,
    //                 'price' => $price,
    //                 'variant_id' => $variant?->id
    //             ],
    //             [
    //                 'quantity' => $newQuantity,
    //                 'price' => $price,
    //                 'total' => $newQuantity * $price
    //             ]
    //         );

    //         Notification::make()
    //             ->title('Product added to cart successfully!')
    //             ->success()
    //             ->send();

    //         // $this->dispatch('cart-updated');

    //     } catch (\Exception $e) {
    //         logger()->error('Error adding to cart: ' . $e->getMessage());

    //         Notification::make()
    //             ->title('Failed to add product to cart')
    //             ->body($e->getMessage())
    //             ->danger()
    //             ->send();
    //     }
    // }

    public function render()
    {
        return view('livewire.product');
    }
}
