<?php

namespace App\Livewire\Seller;

use App\Helper\OrderStatus;
use App\Livewire\Order;
use App\Models\Category;
use App\Models\Order as ModelsOrder;
use App\Models\Printer;
use App\Models\Product;
use App\Models\Variant;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\PdfToImage\Enums\OutputFormat;

#[Layout('layouts.pos')]
class Pos extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms, WithPagination;

    public $search = '';

    public $selectedCategory = null;

    public $cart = [];

    public $total = 0;

    public $discount = 0;

    public $tax = 0; // Default tax percentage

    public $selectedProduct = null;

    // Payment related properties
    public $givenAmount = 0;

    public $changeAmount = 0;

    public $showPaymentForm = false;

    public $lastOrder = null;

    public $showReceipt = false;

    public int $perPage = 24;

    public int $offset = 0;

    public bool $hasMorePages = true;

    public ?Collection $products;

    public function __construct()
    {
        $this->products = new Collection;
    }

    public function mount()
    {
        $this->offset = 0;
        $this->getProductData();
        $this->calculateTotal();
    }

    public function render()
    {
        return view('livewire.seller.pos');
    }

    public function reset(...$properties)
    {
        // Call parent reset method
        parent::reset(...$properties);

        // Reset offset and other pagination related properties
        $this->offset = 0;
        $this->hasMorePages = true;
        $this->products = new Collection;

        // Reload data
        $this->getProductData();
    }

    public function getProductData($loadMore = false)
    {
        try {
            // Check if user is authenticated
            $user = Auth::guard('seller')->user();
            if (! $user) {
                logger()->error('POS: No authenticated seller user found');

                return;
            }

            // Get seller ID for seller-specific filtering
            $sellerId = $user->parent_id ?? $user->id;

            // Start with base query - global tenant scope will handle tenant filtering
            $query = Product::where('seller_id', $sellerId);

            // Apply category filter if selected
            if ($this->selectedCategory) {
                $query->where('category_id', $this->selectedCategory);
            }

            // Apply search filter if provided
            if ($this->search) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%');
                });
            }

            // Add eager loading and ordering
            $query->with(['category', 'brand', 'variants']);

            // Get total count for current query
            $total = $query->count();

            // Calculate if we have more pages
            $this->hasMorePages = $total > ($this->offset + $this->perPage);

            // Get paginated results with offset
            $newProducts = $query->skip($this->offset)->take($this->perPage)
                ->orderBy('id', 'desc')->get();

            if ($loadMore) {
                // Append new products to existing collection
                $this->products = $this->products->concat($newProducts);
            } else {
                // Replace products collection (for fresh load)
                $this->products = $newProducts;
            }
        } catch (Exception $e) {
            $this->products = new Collection;
            $this->hasMorePages = false;

            logger()->error('Error loading products: '.$e->getMessage());
        }
    }

    public function filterByCategory($categoryId)
    {
        // Reset the state
        $this->selectedCategory = $categoryId;
        $this->offset = 0;
        $this->hasMorePages = true;
        $this->resetPage(); // Reset pagination state

        // Initialize new collection
        $this->products = new Collection;

        // Get fresh data
        $this->getProductData();
    }

    public function loadMore(): void
    {
        if (! $this->hasMorePages) {
            return;
        }

        try {
            // Increment offset by perPage to get next batch
            $this->offset += $this->perPage;

            // Load more products and append to existing collection
            $this->getProductData(true);
        } catch (Exception $e) {
            logger()->error('Error loading more products: '.$e->getMessage());
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
    public function categories()
    {
        // Check if user is authenticated
        $user = auth('seller')->user();
        if (! $user) {
            return collect(); // Return empty collection if not authenticated
        }

        // Get seller ID for seller-specific filtering
        $sellerId = $user->parent_id ?? $user->id;

        // Global tenant scope will handle tenant filtering, we add seller filtering
        $categories = Category::where('seller_id', $sellerId)
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
            // Check if user is authenticated
            $user = Auth::guard('seller')->user();
            if (! $user) {
                logger()->error('POS: No authenticated seller user found');

                return;
            }

            // Get seller ID for seller-specific filtering
            $sellerId = $user->parent_id ?? $user->id;

            // Global tenant scope will handle tenant filtering, we add seller filtering
            $product = Product::where('seller_id', $sellerId)
                ->where('id', $productId)
                ->with(['variants'])
                ->first();

            if (! $product) {
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
                    ->body('Minimum order quantity is '.($variant?->MOQ ?? $product->MOQ))
                    ->warning()
                    ->send();

                return;
            }

            // Check stock
            if ($newQuantity > ($variant?->quantity ?? $product->quantity)) {
                Notification::make()
                    ->title('Out of stock')
                    ->body('Only '.($variant?->quantity ?? $product->quantity).' items available')
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
                        : ($product->discount),
                ];
            }

            $this->calculateTotal();
        } catch (Exception $e) {
            logger()->error('Error adding to cart: '.$e->getMessage());

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

        // Recalculate change when total changes
        $this->calculateChange();
    }

    public function calculateChange()
    {
        $this->changeAmount = $this->givenAmount - $this->total;
    }

    public function updatedGivenAmount()
    {
        $this->calculateChange();
    }

    public function setExactAmount()
    {
        $this->givenAmount = $this->total;
        $this->calculateChange();
    }

    public function showPayment()
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Cart is empty')
                ->warning()
                ->send();

            return;
        }

        $this->showPaymentForm = true;
        $this->givenAmount = $this->total; // Set default to exact amount
        $this->calculateChange();
    }

    public function hidePayment()
    {
        $this->showPaymentForm = false;
        $this->givenAmount = 0;
        $this->changeAmount = 0;
    }

    public function setQuickAmount($amount)
    {
        $this->givenAmount = $amount;
        $this->calculateChange();
    }

    public function closeReceipt()
    {
        $this->showReceipt = false;
        $this->lastOrder = null;
    }

    public function placeOrder()
    {
        try {
            // Check if user is authenticated
            $user = auth('seller')->user();
            if (! $user) {
                Notification::make()
                    ->title('Authentication required')
                    ->danger()
                    ->send();

                return;
            }

            if (empty($this->cart)) {
                Notification::make()
                    ->title('Cart is empty')
                    ->warning()
                    ->send();

                return;
            }

            // Check if given amount is sufficient (only if payment form is shown)
            if ($this->showPaymentForm && $this->givenAmount < $this->total) {
                Notification::make()
                    ->title('Insufficient Payment')
                    ->body('Given amount is less than the total amount')
                    ->warning()
                    ->send();

                return;
            }

            // Start a database transaction
            DB::beginTransaction();

            $sellerId = $user->parent_id ?? $user->id;
            $staffId = $user->id;

            // Create the order
            $order = ModelsOrder::create([
                'invoice_number' => 'INV-'.time().'-'.$staffId,
                'seller_id' => $sellerId,
                'staff_id' => $staffId, // Current logged in seller as staff
                'walk_in_customer' => true, // Default to walk-in customer for POS
                'is_cod' => true, // Cash on delivery for POS
                'status' => OrderStatus::Delivered, // Assuming POS sales are completed immediately
                'sub_total' => collect($this->cart)->sum('subtotal'),
                'delivery_fees' => 0, // No delivery for POS
                'total_price' => $this->total,
                'tax' => $this->tax,
                'tax_price' => collect($this->cart)->sum('subtotal') * ($this->tax / 100),
                'given_amount' => $this->givenAmount,
                'change_amount' => $this->changeAmount,
            ]);

            // Create order products
            foreach ($this->cart as $item) {
                $product = Product::find($item['product_id']);
                $variant = isset($item['variant_id']) ? Variant::find($item['variant_id']) : null;

                if ($variant) {
                    if ($item['quantity'] > $variant->quantity) {
                        throw new Exception($product->name.' stock is not enough');
                    }
                } else {
                    if ($item['quantity'] > $product->quantity) {
                        throw new Exception($product->name.' stock is not enough');
                    }
                }

                // Create order product
                $order->products()->create([
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'attributes' => $variant ? $variant->attributeValues : $product->attributeValues,
                    'price' => $variant ? $variant->mmk_price : $product->mmk_price,
                    'discount_price' => $variant ? $variant->mmk_discount_price : $product->mmk_discount_price,
                    'discount' => $item['discount_percentage'],
                    'quantity' => $item['quantity'],

                    // 'profit' => $item['discount_percentage'] > 0 ?
                    //     ($item['price'] * $item['quantity']) - (($variant ? $variant->mmk_buy_price : $product->mmk_buy_price) * $item['quantity']) :
                    //     ($item['price'] * $item['quantity']) - (($variant ? $variant->mmk_buy_price : $product->mmk_buy_price) * $item['quantity']),
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

            // Store order for receipt display
            $this->lastOrder = ModelsOrder::with(['products', 'products.variant', 'products.product'])
                ->where('id', $order->id)
                ->first();
            $this->showReceipt = true;

            // Reset cart and payment fields after order
            $this->cart = [];
            $this->discount = 0;
            $this->givenAmount = 0;
            $this->changeAmount = 0;
            $this->showPaymentForm = false;
            $this->calculateTotal();

            Notification::make()
                ->title('Order placed successfully!')
                ->body('Invoice #: '.$order->invoice_number)
                ->success()
                ->send();
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            logger()->error('Error creating order: '.$e->getMessage());

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
            $user = Auth::guard('seller')->user();
            if (! $user) {
                return;
            }

            // Get seller ID for seller-specific filtering
            $sellerId = $user->parent_id ?? $user->id;

            // Global tenant scope will handle tenant filtering, we add seller filtering
            $product = Product::where('seller_id', $sellerId)->find($productId);

            if (! $product) {
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
                    ->body('Only '.$availableStock.' items available')
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

            $user = Auth::guard('seller')->user();
            if (! $user) {
                return;
            }

            // Get seller ID for seller-specific filtering
            $sellerId = $user->parent_id ?? $user->id;

            // Global tenant scope will handle tenant filtering, we add seller filtering
            $product = Product::where('seller_id', $sellerId)->find($productId);

            if (! $product) {
                return;
            }

            $variant = $variantId ? Variant::find($variantId) : null;
            $moq = $variant ? ($variant->MOQ ?? 1) : ($product->MOQ ?? 1);

            // Check if new quantity is below MOQ
            if ($newQuantity < $moq) {
                Notification::make()
                    ->title('Minimum order quantity')
                    ->body('Minimum order quantity is '.$moq)
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
                ['selectedProduct' => Product::with('variants')->find($arguments['record'])],
            ));
    }

    public function searchProducts()
    {
        // Reset pagination when searching
        $this->resetPage();
        $this->offset = 0;
        $this->hasMorePages = true;

        // Reset products collection
        $this->products = new Collection;

        // Get fresh data with search term
        $this->getProductData();
    }

    public function searchByBarcodeAndAddToCart()
    {
        // Check if user is authenticated
        $user = Auth::guard('seller')->user();
        if (! $user) {
            logger()->error('POS: No authenticated seller user found');

            return;
        }

        // Get seller ID for seller-specific filtering
        $sellerId = $user->parent_id ?? $user->id;

        // Search for product by barcode/SKU - global tenant scope will handle tenant filtering
        $product = Product::where('seller_id', $sellerId)
            ->where(function ($query) {
                $query->where('sku', $this->search)
                    ->orWhere('sku', $this->search);
            })
            ->first();

        if ($product) {
            // If product found, add to cart
            $this->addToCart($product->id, null, $product->MOQ);
        } else {
            $variant = Variant::whereHas('product', function ($query) use ($sellerId) {
                // Global tenant scope will handle tenant filtering for the product relationship
                $query->where('seller_id', $sellerId);
            })
                ->where('sku', $this->search)
                ->with('product')
                ->first();

            if ($variant) {
                // If variant found, add to cart
                $this->addToCart($variant->product->id, $variant->id, $variant->MOQ);

                $this->search = '';
                $this->searchProducts();
            }
        }
    }

    /**
     * Generate raw receipt for direct thermal printer printing
     * Full width design with clean layout
     */
    public function generateRawReceiptPrint($orderId): void
    {
        try {
            $order = ModelsOrder::with(['products', 'products.variant', 'products.product', 'seller'])
                ->where('id', $orderId)
                ->first();

            if (! $order) {
                Notification::make()
                    ->title('Order not found')
                    ->danger()
                    ->send();

                return;
            }

            // Get the seller's default printer configuration
            $user = Auth::guard('seller')->user();
            if (! $user) {
                throw new Exception('No authenticated seller user found');
            }

            $sellerId = $user->parent_id ?? $user->id;
            $printerConfig = Printer::where('seller_id', $sellerId)
                ->where('is_default', true)
                ->where('is_active', true)
                ->first();

            if (! $printerConfig) {
                Notification::make()
                    ->title('No Printer Found')
                    ->body('Please configure a default printer first.')
                    ->warning()
                    ->send();

                return;
            }

            // Create printer connector
            $connector = $this->createPrinterConnector($printerConfig);
            if (! $connector) {
                throw new Exception('Could not create printer connector');
            }

            $printer = new \Mike42\Escpos\Printer($connector);

            // Initialize printer
            $printer->initialize();
            $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_A);

            // Print receipt header
            $this->printReceiptHeader($printer, $order);

            // Print invoice information
            $this->printInvoiceInfo($printer, $order);

            // Print separator
            $this->printSeparator($printer);

            // Print items
            $this->printReceiptItems($printer, $order);

            // Print totals
            $this->printReceiptTotals($printer, $order);

            // Print payment info
            if ($order->given_amount > 0) {
                $this->printPaymentInfo($printer, $order);
            }

            // Print footer
            $this->printReceiptFooter($printer);

            // Feed and cut
            $printer->feed(3);
            $printer->cut(\Mike42\Escpos\Printer::CUT_FULL);
            $printer->close();

            Notification::make()
                ->title('Receipt printed successfully')
                ->success()
                ->send();

        } catch (Exception $e) {
            logger()->error('Error generating raw receipt print: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'order_id' => $orderId,
            ]);

            Notification::make()
                ->title('Failed to print receipt')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Print receipt header with store information
     */
    private function printReceiptHeader($printer, $order): void
    {
        // Store name - centered and bold
        $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_A | \Mike42\Escpos\Printer::MODE_EMPHASIZED);
        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
        $printer->text($this->convertMyanmarToEnglish($order->seller->name)."\n");

        // Store address
        $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_A);
        if ($order->seller->addresses->count() > 0) {
            $address = $order->seller->addresses->first()->modAddress ?? '';
            $printer->text($this->convertMyanmarToEnglish($address)."\n");
        }

        // Contact info
        $contactInfo = '';
        if ($order->seller->phone) {
            $contactInfo .= $order->seller->phone;
        }
        if ($order->seller->email) {
            $contactInfo .= ($contactInfo ? ' , ' : '').$order->seller->email;
        }
        if ($contactInfo) {
            $printer->text($contactInfo."\n");
        }

        $printer->feed(1);
    }

    /**
     * Print invoice information
     */
    private function printInvoiceInfo($printer, $order): void
    {
        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_LEFT);
        $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_A);

        $printer->text('Invoice: '.$order->invoice_number."\n");
        $printer->text('Date: '.$order->created_at->format('M d, Y H:i')."\n");
        $printer->text('Cashier: '.($order->staff->name ?? 'POS Staff')."\n");
    }

    /**
     * Print separator line
     */
    private function printSeparator($printer): void
    {
        $printer->text(str_repeat('-', 48)."\n");
    }

    /**
     * Print receipt items with clean formatting
     */
    private function printReceiptItems($printer, $order): void
    {
        $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_A | \Mike42\Escpos\Printer::MODE_EMPHASIZED);
        $printer->text("ITEMS:\n");
        $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_A);
        $printer->feed(1);

        foreach ($order->products as $item) {
            // Product name - convert Myanmar to English
            $productName = $this->convertMyanmarToEnglish($item->product->name);
            $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_A | \Mike42\Escpos\Printer::MODE_EMPHASIZED);
            $printer->text($productName."\n");

            // Variant info if exists
            if ($item->variant) {
                $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_B);
                $variantName = $this->convertMyanmarToEnglish($item->variant->name);
                $printer->text('Variant: '.$variantName."\n");
            }

            // Price and quantity line
            $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_A);

            if ($item->discount > 0) {
                $priceLine = sprintf(
                    '%d x %s MMK (%d%% off)',
                    $item->quantity,
                    number_format($item->discount_price),
                    $item->discount
                );
            } else {
                $priceLine = sprintf(
                    '%d x %s MMK',
                    $item->quantity,
                    number_format($item->price)
                );
            }

            // Format price line with total on the right
            $totalPrice = number_format($item->sub_price).' MMK';
            $spacePadding = 48 - strlen($priceLine) - strlen($totalPrice);
            $spacePadding = max(1, $spacePadding); // At least 1 space

            $printer->text($priceLine.str_repeat(' ', $spacePadding).$totalPrice."\n");
            $printer->feed(1);
        }
    }

    /**
     * Print receipt totals section
     */
    private function printReceiptTotals($printer, $order): void
    {
        $printer->text(str_repeat('-', 48)."\n");

        // Subtotal
        $this->printRightAlignedRow($printer, 'Subtotal:', number_format($order->sub_total).' MMK');

        // Tax if applicable
        if ($order->tax_price > 0) {
            $this->printRightAlignedRow($printer, "Tax ({$order->tax}%):", number_format($order->tax_price).' MMK');
        }

        // Grand total
        $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_A | \Mike42\Escpos\Printer::MODE_EMPHASIZED);
        $this->printRightAlignedRow($printer, 'TOTAL:', number_format($order->total_price).' MMK');
        $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_A);
    }

    /**
     * Print payment information
     */
    private function printPaymentInfo($printer, $order): void
    {
        $printer->feed(1);
        $this->printRightAlignedRow($printer, 'Given Amount:', '- '.number_format($order->given_amount).' MMK');
        $this->printRightAlignedRow($printer, 'Change:', number_format($order->change_amount).' MMK');
    }

    /**
     * Print receipt footer
     */
    private function printReceiptFooter($printer): void
    {
        $printer->feed(2);
        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(\Mike42\Escpos\Printer::MODE_FONT_A);

        $printer->text("Thank You!\n");
        $printer->text("Please come back again\n");
        $printer->feed(1);
        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_LEFT);
    }

    /**
     * Print right-aligned row with label and value
     */
    private function printRightAlignedRow($printer, $label, $value): void
    {
        $totalWidth = 48;
        $spacePadding = $totalWidth - strlen($label) - strlen($value);
        $spacePadding = max(1, $spacePadding);

        $printer->text($label.str_repeat(' ', $spacePadding).$value."\n");
    }

    /**
     * Convert Myanmar text to English using Str::slug without hyphens
     * Example: 'သံပုရာသီး' becomes 'orange' (transliterated/slugified)
     */
    private function convertMyanmarToEnglish($text): string
    {
        // For other Myanmar text, use Str::slug and remove hyphens
        $slugged = Str::slug($text, ' ');

        // If slug is empty or just spaces, return original text
        if (empty(trim($slugged))) {
            return $text;
        }

        return $slugged;
    }

    public function generateReceiptPdf($orderId): void
    {
        try {
            $order = ModelsOrder::with(['products', 'products.variant', 'products.product'])
                ->where('id', $orderId)
                ->first();

            if (! $order) {
                Notification::make()
                    ->title('Order not found')
                    ->danger()
                    ->send();

                return;
            }

            // Ensure receipts directory exists with proper permissions
            $receiptsDir = public_path('receipts');
            if (! file_exists($receiptsDir)) {
                mkdir($receiptsDir, 0755, true);
            }

            // Calculate dynamic height
            $baseHeight = 70;
            $itemHeight = 9;
            $productCount = $order->products->count();
            $dynamicHeight = $baseHeight + ($productCount * $itemHeight);
            $minHeight = 80;
            $maxHeight = 300;
            $finalHeight = max($minHeight, min($maxHeight, $dynamicHeight));

            // Generate PDF using environment variables
            $pdf = Pdf::view('pdf.receipt', ['order' => $order])
                ->paperSize(70, $finalHeight, 'mm')
                ->margins(0, 0, 0, 0);

            // ->withBrowsershot(function (\Spatie\Browsershot\Browsershot $browsershot) {
            //     $browsershot
            //         ->setNodeBinary(config('laravel-pdf.node_binary', '/usr/bin/node'))
            //         ->setNpmBinary(config('laravel-pdf.npm_binary', '/usr/bin/npm'))
            //         ->setIncludePath(config('laravel-pdf.include_path', '/usr/local/bin:/usr/bin:/bin'))
            //         ->setChromePath(config('laravel-pdf.chrome_path', '/usr/bin/chromium-browser'))
            //         ->setNodeModulePath(config('laravel-pdf.node_modules_path', '/usr/local/lib/node_modules'))
            //         ->timeout(120)
            //         ->addChromiumArguments([
            //             // Critical crashpad fix - prevents database required error
            //             '--disable-crashpad',
            //             '--disable-crash-reporter',
            //             '--disable-breakpad',
            //             '--no-crash-upload',
            //             '--disable-crash-handler',
            //             '--crash-dumps-dir=/tmp',

            //             // Essential security flags for production
            //             '--no-sandbox',
            //             '--disable-setuid-sandbox',
            //             '--disable-dev-shm-usage',
            //             '--disable-namespace-sandbox',
            //             '--disable-seccomp-filter-sandbox',
            //             '--disable-sandbox',

            //             // Process management
            //             '--disable-zygote',
            //             '--no-zygote',
            //             '--single-process',
            //             '--disable-background-timer-throttling',
            //             '--disable-renderer-backgrounding',
            //             '--disable-backgrounding-occluded-windows',
            //             '--disable-ipc-flooding-protection',

            //             // System service disabling
            //             '--disable-dbus',
            //             '--disable-audio-service-sandbox',
            //             '--disable-background-networking',
            //             '--disable-client-side-phishing-detection',
            //             '--disable-component-extensions-with-background-pages',
            //             '--disable-component-update',
            //             '--disable-default-apps',
            //             '--disable-hang-monitor',
            //             '--disable-popup-blocking',
            //             '--disable-prompt-on-repost',
            //             '--disable-sync',
            //             '--disable-translate',
            //             '--disable-web-security',

            //             // GPU and rendering
            //             '--disable-gpu',
            //             '--disable-gpu-sandbox',
            //             '--disable-software-rasterizer',
            //             '--disable-extensions',
            //             '--disable-plugins',
            //             '--disable-features=VizDisplayCompositor,AudioServiceOutOfProcess,TranslateUI',
            //             '--run-all-compositor-stages-before-draw',

            //             // Headless configuration
            //             '--headless=new',
            //             '--virtual-time-budget=5000',

            //             // Memory optimization
            //             '--memory-pressure-off',
            //             '--max_old_space_size=4096',

            //             // Performance optimization
            //             '--hide-scrollbars',
            //             '--metrics-recording-only',
            //             '--mute-audio',
            //             '--no-first-run',
            //             '--safebrowsing-disable-auto-update',
            //             '--disable-logging',
            //             '--disable-metrics',
            //             '--disable-metrics-reporting',

            //             // SSL/Certificate handling
            //             '--ignore-certificate-errors',
            //             '--ignore-ssl-errors',
            //             '--ignore-certificate-errors-spki-list',
            //             '--allow-running-insecure-content',

            //             // Temporary directories
            //             '--user-data-dir=/tmp/chromium-user-data',
            //             '--data-path=/tmp/chromium-data',
            //             '--homedir=/tmp',
            //             '--disk-cache-dir=/tmp/chromium-cache',

            //             // Font configuration for Myanmar text
            //             '--font-render-hinting=none',
            //             '--disable-font-subpixel-positioning',
            //             '--disable-lcd-text',

            //             // Additional stability flags
            //             '--disable-field-trial-config',
            //             '--disable-back-forward-cache',
            //             '--no-default-browser-check',
            //         ]);
            // });

            // Save PDF to file
            $pdfPath = public_path("receipts/receipt-{$order->invoice_number}.pdf");
            $pdf->save($pdfPath);

            return;
            // Return PDF for download/print
            if (file_exists($pdfPath)) {
                // Attempt to print the PDF directly to the thermal printer
                $this->printReceiptFallback($order, $pdfPath, null);

                // Optionally, you can also return the PDF for download
                // return response()->download($pdfPath);
            } else {
                Notification::make()
                    ->title('PDF generation failed')
                    ->body('Could not generate receipt PDF.')
                    ->danger()
                    ->send();
            }

        } catch (Exception $e) {
            logger()->error('Error generating PDF: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'order_id' => $orderId,
            ]);

            Notification::make()
                ->title('Failed to generate PDF')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function printReceiptFallback($order, $pdfPath, $primaryError)
    {
        try {
            // Get the seller's default printer configuration
            $user = Auth::guard('seller')->user();
            if (! $user) {
                throw new Exception('No authenticated seller user found');
            }

            $sellerId = $user->parent_id ?? $user->id;
            $printerConfig = Printer::where('seller_id', $sellerId)
                ->where('is_default', true)
                ->where('is_active', true)
                ->first();

            if (! $printerConfig) {
                Notification::make()
                    ->title('No printer configured')
                    ->body('Please configure a default printer in settings.')
                    ->warning()
                    ->send();

                return;
            }

            // Create printer connector based on configuration
            $connector = $this->createPrinterConnector($printerConfig);
            if (! $connector) {
                throw new Exception('Could not create printer connector');
            }

            if (env('APP_ENV') !== 'production') {
                // Set Ghostscript path for ImageMagick
                putenv('PATH=/opt/homebrew/bin:'.getenv('PATH'));

                // Also try setting GS_PROG for ImageMagick
                putenv('GS_PROG=/opt/homebrew/bin/gs');
            }

            // Try PDF-to-image conversion first for better quality printing
            $imagePath = $this->convertPdfToImage($pdfPath, $order);

            if ($imagePath && file_exists($imagePath)) {
                $printer = new \Mike42\Escpos\Printer($connector);

                // Load and print the converted image
                $img = EscposImage::load($imagePath);
                $printer->bitImageColumnFormat($img);

                usleep(500000); // 0.5 seconds

                // Ensure the device finishes raster processing
                $printer->text("\n");
                usleep(300000); // 300 ms

                // Some models need re-init before cut after graphics
                $printer->initialize();

                // Advance to cutter and try both cut modes
                $printer->feed(3);
                $printer->cut(\Mike42\Escpos\Printer::CUT_FULL);

                usleep(200000); // let the cut execute
                $printer->close();

                // Clean up temporary image file
                if (file_exists($imagePath)) {
                    // unlink($imagePath);
                }

                // remove pdf after printing
                if (file_exists($pdfPath)) {
                    // unlink($pdfPath);
                }

                Notification::make()
                    ->title('Receipt printed successfully')
                    ->body("Printed to: {$printerConfig->name}")
                    ->success()
                    ->send();

                return;
            }

            throw new Exception('PDF-to-image conversion failed');
        } catch (Exception $fallbackError) {
            logger()->error('Printing failed: '.$fallbackError->getMessage());

            Notification::make()
                ->title('Printing failed')
                ->body('Could not print receipt. Please check printer configuration.')
                ->danger()
                ->send();
        }
    }

    private function createPrinterConnector($printerConfig)
    {
        try {
            return match ($printerConfig->connection_type) {
                'network' => new NetworkPrintConnector(
                    $printerConfig->ip_address,
                    $printerConfig->port
                ),
                'usb' => new FilePrintConnector($printerConfig->usb_path),
                default => throw new Exception('Unsupported printer connection type'),
            };
        } catch (Exception $e) {
            logger()->error('Error creating printer connector: '.$e->getMessage());

            return null;
        }
    }

    private function convertPdfToImage($pdfPath, $order)
    {
        try {
            if (! file_exists($pdfPath)) {
                return null;
            }

            // Convert PDF to image using Spatie package
            $imagePath = storage_path("app/temp-receipt-{$order->id}.png"); // Changed from .png

            $pdfToImage = new \Spatie\PdfToImage\Pdf($pdfPath);
            $pdfToImage
                ->format(OutputFormat::Png)
                ->resolution(203)
                ->quality(10)
                ->save($imagePath);

            return $imagePath;

        } catch (Exception $e) {
            logger()->error('PDF to image conversion failed: '.$e->getMessage());

            return null;
        }
    }
}
