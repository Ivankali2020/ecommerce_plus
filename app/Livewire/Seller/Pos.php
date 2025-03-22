<?php

namespace App\Livewire\Seller;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Product;
use App\Models\Category;
use App\Models\Variant;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.pos')]
class Pos extends Component implements HasForms, HasActions
{
    use WithPagination, InteractsWithActions, InteractsWithForms;
    
    public $search = '';
    public $selectedCategory = null;
    public $cart = [];
    public $total = 0;
    public $discount = 0;
    public $tax = 0; // Default tax percentage
    public $selectedProduct = null;
    public int $perPage = 24;
    public bool $hasMorePages = true;
    public ?Collection $products;

    public function __construct()
    {
        $this->products = new Collection();
    }
    
    public function mount()
    {
        $this->getProductData();
        $this->calculateTotal();
    }
    
    public function render()    
    {
        return view('livewire.seller.pos');
    }

    public function getProductData()
    {
        try {
            $query = Product::where('seller_id', Auth::guard('seller')->user()->parent_id ?? Auth::guard('seller')->user()->id);
            
            // Apply category filter if selected
            if ($this->selectedCategory) {
                $query->where('category_id', $this->selectedCategory);
            }
            
            // Apply search filter if provided
            if ($this->search) {
                $query->where(function($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            }
            
            // Add eager loading and ordering
            $query->with(['category', 'brand', 'variants'])
                  ->latest();

            // Get total count for current query
            $total = $query->count();

            // Calculate if we have more pages
            $this->hasMorePages = $total >= $this->perPage;

            // Get paginated results
            $newProducts = $query->take($this->perPage)->get();

            $this->products = $newProducts;

        } catch (\Exception $e) {
            $this->products = new Collection();
            $this->hasMorePages = false;

            logger()->error('Error loading products: ' . $e->getMessage());
        }
    }

    public function filterByCategory($categoryId)
    {
        // Reset the state
        $this->selectedCategory = $categoryId;
        $this->perPage = 18;
        $this->hasMorePages = true;
        $this->resetPage(); // Reset pagination state

        // Initialize new collection
        $this->products = new Collection();

        // Get fresh data
        $this->getProductData();
    }

    public function loadMore(): void
    {
        if (!$this->hasMorePages) {
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


    // #[Computed]
    // public function products(){
    //     $productsQuery = Product::where('seller_id', auth('seller')->id())
            
            
    //     if ($this->categoryFilter) {
    //         $productsQuery->where('category_id', $this->categoryFilter);
    //     }
        
    //     $products = $productsQuery->paginate(8);

    //     return $products;

    // }

    #[Computed]
    public function categories(){
       
        $categories = Category::where('seller_id', auth('seller')->id())
        ->withCount('products')->get()
        ->where('products_count', '>', 0);

        return collect($categories);
    }
    
    public function showProductDetail($productId)
    {
        $this->selectedProduct = Product::with('variants')->findOrFail($productId);
        $this->dispatch('openProductModal', $this->selectedProduct);
    }
    
    public function addToCart($productId, $variantId = null, $quantity = 1)
    {
        try {
            $product = Product::where('id', $productId)
                ->with(['variants'])
                ->first();

            if (!$product) {
                Notification::make()
                    ->title('Product not found')
                    ->danger()
                    ->send();
                return;
            }

            $variant = $variantId
                ? $product->variants->find($variantId)
                : ($product->has_variants ? $product->variants->first() : null);

            $price = $variant
                ? ($variant->discount > 0 ? $variant->mmk_discount_price : $variant->mmk_price)
                : ($product->discount > 0 ? $product->mmk_discount_price : $product->mmk_price);

            // Check if product already exists in cart
            $existingItemKey = $this->findCartItem($productId, $variantId);
            
            $newQuantity = $existingItemKey !== false ? $this->cart[$existingItemKey]['quantity'] + $quantity : $quantity;
            
            // Check MOQ
            if ($newQuantity < ($variant?->MOQ ?? $product->MOQ)) {
                Notification::make()
                    ->title('Minimum order quantity not met')
                    ->body('Minimum order quantity is ' . ($variant?->MOQ ?? $product->MOQ))
                    ->warning()
                    ->send();
                return;
            }
            
            // Check stock
            if ($newQuantity > ($variant?->quantity ?? $product->quantity)) {
                Notification::make()
                    ->title('Out of stock')
                    ->body('Only ' . ($variant?->quantity ?? $product->quantity) . ' items available')
                    ->warning()
                    ->send();
                return;
            }

            // Update or create cart item
            if ($existingItemKey !== false) {
                $this->cart[$existingItemKey]['quantity'] = $newQuantity;
                $this->cart[$existingItemKey]['subtotal'] = $price * $newQuantity;
               
            } else {
                $this->cart[] = [
                    'id' => $productId,
                    'product_id' => $productId,
                    'name' => $variant ? $variant->name : $product->name,
                    'price' => $price,
                    'quantity' => $newQuantity,
                    'variant_id' => $variant?->id,
                    'subtotal' => $price * $newQuantity,
                    'discount_percentage' => $variant ? $variant->discount : $product->discount,
                    'original_price' => $variant ? $variant->mmk_price : $product->mmk_price,
                    'has_discount' => $variant 
                        ? ($variant->discount) 
                        : ($product->discount)
                ];
                
            }
            
            $this->calculateTotal();

        } catch (\Exception $e) {
            logger()->error('Error adding to cart: ' . $e->getMessage());
            
            Notification::make()
                ->title('Failed to add product to cart')
                ->body($e->getMessage())
                ->danger()
                ->duration(500)
                ->send();
        }
    }
    
    public function findCartItem($productId, $variantId = null)
    {
        foreach ($this->cart as $key => $item) {
            if ($item['product_id'] == $productId && $item['variant_id'] == $variantId) {
                return $key;
            }
        }
        
        return false;
    }
    
    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->calculateTotal();
    }
    
    public function updateQuantity($index, $quantity)
    {
        if ($quantity > 0) {
            $this->cart[$index]['quantity'] = $quantity;
            $this->cart[$index]['subtotal'] = $this->cart[$index]['price'] * $quantity;
            $this->calculateTotal();
        }
    }
    
    public function calculateTotal()
    {
        $subtotal = 0;
        foreach ($this->cart as $item) {
            $subtotal += $item['subtotal'];
        }
        
        $taxAmount = $subtotal * ($this->tax / 100);
        $this->total = $subtotal - $this->discount + $taxAmount;
    }
    
    public function placeOrder()
    {
        try {
            if (empty($this->cart)) {
                Notification::make()
                    ->title('Cart is empty')
                    ->warning()
                    ->send();
                return;
            }
            
            // Start a database transaction
            DB::beginTransaction();
            
            // Create the order
            $order = \App\Models\Order::create([
                'invoice_number' => 'INV-' . time() . '-' . auth('seller')->id(),
                'seller_id' => auth('seller')->user()->parent_id ?? auth('seller')->id(),
                'staff_id' => auth('seller')->id(), // Current logged in seller as staff
                'walk_in_customer' => true, // Default to walk-in customer for POS
                'is_cod' => true, // Cash on delivery for POS
                'status' => \App\Helper\OrderStatus::Delivered, // Assuming POS sales are completed immediately
                'sub_total' => collect($this->cart)->sum('subtotal'),
                'delivery_fees' => 0, // No delivery for POS
                'total_price' => $this->total,
                'tax' => $this->tax,
                'tax_price' => collect($this->cart)->sum('subtotal') * ($this->tax / 100),
            ]);
            
            // Create order products
            foreach ($this->cart as $item) {
                $product = Product::find($item['product_id']);
                $variant = isset($item['variant_id']) ? Variant::find($item['variant_id']) : null;

                if ($variant) {
                    if ($item['quantity'] > $variant->quantity) {
                        throw new Exception($product->name . ' stock is not enough');
                    }
                } else {
                    if ($item['quantity'] > $product->quantity) {
                        throw new Exception($product->name . ' stock is not enough');
                    }
                }
                
                // Create order product
                $order->products()->create([
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'attributes' => $variant ? $variant->attributeValues : $product->attributeValues, 
                    'price' => $variant ? $variant->mmk_price : $product->mmk_price,
                    'discount_price' => $variant ? $variant->mmk_discount_price : $product->mmk_discount_price, // No additional discount in POS
                    'discount' => $item['discount_percentage'], // No additional discount in POS
                    'quantity' => $item['quantity'],
                    'original_buy_price' => $variant ? $variant->buy_price : $product->buy_price,
                    'original_price' => $variant ? $variant->mmk_price : $product->mmk_price,
                    'original_discount_price' => $variant ? $variant->mmk_discount_price : $product->mmk_discount_price,
                    'currency_id' => 1, // Assuming MMK is your default currency with ID 1
                    'exchange_rate' => $variant ? $variant->product->exchange->rate : $product->exchange->rate, // Default exchange rate
                    'sub_price' => $item['subtotal'],
                ]);
              
            }
            
            // Commit the transaction
            DB::commit();
            
            // Reset cart after order
            $this->cart = [];
            $this->discount = 0;
            $this->calculateTotal();
            
            Notification::make()
                ->title('Order placed successfully!')
                ->body('Invoice #: ' . $order->invoice_number)
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            
            logger()->error('Error creating order: ' . $e->getMessage());
            
            Notification::make()
                ->title('Failed to place order')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function incrementQuantity($index)
    {
        if (isset($this->cart[$index])) {
            $productId = $this->cart[$index]['product_id'];
            $variantId = $this->cart[$index]['variant_id'];
            $newQuantity = $this->cart[$index]['quantity'] + 1;
            
            // Check if we have enough stock
            $product = Product::find($productId);
            if (!$product) {
                Notification::make()
                    ->title('Product not found')
                    ->danger()
                    ->send();
                return;
            }
            
            $variant = $variantId ? Variant::find($variantId) : null;
            $availableStock = $variant ? $variant->quantity : $product->quantity;
            
            if ($newQuantity > $availableStock) {
                Notification::make()
                    ->title('Stock limit reached')
                    ->body('Only ' . $availableStock . ' items available')
                    ->warning()
                    ->send();
                return;
            }
            
            // Update quantity if stock is available
            $this->cart[$index]['quantity'] = $newQuantity;
            $this->cart[$index]['subtotal'] = $this->cart[$index]['price'] * $newQuantity;
            $this->calculateTotal();
        }
    }
    
    public function decrementQuantity($index)
    {
        if (isset($this->cart[$index]) && $this->cart[$index]['quantity'] > 1) {
            // Get product to check MOQ
            $productId = $this->cart[$index]['product_id'];
            $variantId = $this->cart[$index]['variant_id'];
            $newQuantity = $this->cart[$index]['quantity'] - 1;
            
            $product = Product::find($productId);
            if (!$product) {
                return;
            }
            
            $variant = $variantId ? Variant::find($variantId) : null;
            $moq = $variant ? ($variant->MOQ ?? 1) : ($product->MOQ ?? 1);
            
            // Check if new quantity is below MOQ
            if ($newQuantity < $moq) {
                Notification::make()
                    ->title('Minimum order quantity')
                    ->body('Minimum order quantity is ' . $moq)
                    ->warning()
                    ->send();
                return;
            }
            
            $this->cart[$index]['quantity'] = $newQuantity;
            $this->cart[$index]['subtotal'] = $this->cart[$index]['price'] * $newQuantity;
            $this->calculateTotal();
        }
    }


    public function variantAction(): Action
    {
        return Action::make('variant')
            ->label('မျိုးကွဲများ')
            ->size('sm')->outlined()->color('gray')
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalContent(fn (array $arguments) => View(
                'livewire.seller.product-detail-modal',
                ['selectedProduct' => \App\Models\Product::with('variants')->find($arguments['record'])],
            ));
    }


    public function searchProducts()
    {
        // Reset pagination when searching
        $this->resetPage();
        
        // Reset products collection
        $this->products = new Collection();
        
        // Get fresh data with search term
        $this->getProductData();
    }
    
    public function searchByBarcodeAndAddToCart()
    {
        
        // Search for product by barcode/SKU
        $product = Product::where('seller_id',  Auth::guard('seller')->user()->parent_id ?? Auth::guard('seller')->user()->id )
            ->where(function($query) {
                $query->where('sku', $this->search)
                    ->orWhere('sku', $this->search);
            })
            ->first();
        
        if ($product) {
            // If product found, add to cart
            $this->addToCart($product->id, null, $product->MOQ);
            
        } else {

            $variant = Variant::whereRelation('product','seller_id', Auth::guard('seller')->user()->parent_id ?? Auth::guard('seller')->user()->id)
                ->where('sku', $this->search)
                ->with('product')
                ->first();

                if($variant){
                    // If product found, add to cart
                    $this->addToCart($variant->product->id, $variant->id, $variant->MOQ);

                    $this->search = '';
                    $this->searchProducts();
                }

          
        }
        
        return;
    }
}


