<?php

namespace App\Livewire\Seller;

use Exception;
use App\Livewire\Order;
use App\Models\Product;
use App\Models\Variant;
use Livewire\Component;
use App\Models\Category;
use App\Helper\OrderStatus;
use Filament\Actions\Action;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelPdf\Enums\Format;
use App\Models\Order as ModelsOrder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Spatie\PdfToImage\Enums\OutputFormat;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;

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
            $order = \App\Models\Order::create([
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

    public function generateReceiptPdf($orderId)
    {
        try {
            // Find the order
            $order = ModelsOrder::with(['products', 'products.variant', 'products.product'])
                ->where('id', $orderId)
                ->first();

            if (!$order) {
                Notification::make()
                    ->title('Order not found')
                    ->danger()
                    ->send();
                return;
            }

            // Ensure receipts directory exists
            $receiptsDir = public_path('receipts');
            if (!file_exists($receiptsDir)) {
                mkdir($receiptsDir, 0755, true);
            }

            // Calculate dynamic height based on products count
            $baseHeight = 70; // Base height in mm (reduced from 150)
            $itemHeight = 9; // Height per item in mm (reduced from 12)
            $productCount = $order->products->count();
            $dynamicHeight = $baseHeight + ($productCount * $itemHeight);
            
            // Minimum and maximum height constraints
            $minHeight = 80; // Reduced minimum height
            $maxHeight = 300; // Reduced maximum height
            $finalHeight = max($minHeight, min($maxHeight, $dynamicHeight));

            // Generate PDF for 3-inch thermal printer (75mm width, dynamic height)
            $pdf = Pdf::view('pdf.receipt', ['order' => $order])
                ->paperSize(70, $finalHeight, 'mm')
                ->margins(0, 0, 0, 0);

            // Save PDF to file
            $pdfPath = public_path("receipts/receipt-{$order->invoice_number}.pdf");
            $pdf->save($pdfPath);

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
            logger()->error('Error generating PDF: ' . $e->getMessage());
            
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
            if (!$user) {
                throw new Exception('No authenticated seller user found');
            }

            $sellerId = $user->parent_id ?? $user->id;
            $printerConfig = \App\Models\Printer::where('seller_id', $sellerId)
                ->where('is_default', true)
                ->where('is_active', true)
                ->first();

            if (!$printerConfig) {
                Notification::make()
                    ->title('No printer configured')
                    ->body('Please configure a default printer in settings.')
                    ->warning()
                    ->send();
                return;
            }

            // Create printer connector based on configuration
            $connector = $this->createPrinterConnector($printerConfig);
            if (!$connector) {
                throw new Exception('Could not create printer connector');
            }

            if(env('APP_ENV') !== 'production'){
                // Set Ghostscript path for ImageMagick
                putenv('PATH=/opt/homebrew/bin:' . getenv('PATH'));
                
                // Also try setting GS_PROG for ImageMagick
                putenv('GS_PROG=/opt/homebrew/bin/gs');
            }

            // Try PDF-to-image conversion first for better quality printing
            $imagePath = $this->convertPdfToImage($pdfPath, $order);
            
            if ($imagePath && file_exists($imagePath)) {
                $printer = new \Mike42\Escpos\Printer($connector);

                // Load and print the converted image
                $img = \Mike42\Escpos\EscposImage::load($imagePath);
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
                    unlink($imagePath);
                }

                //remove pdf after printing
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
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
            logger()->error('Printing failed: ' . $fallbackError->getMessage());
            
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
                'network' => new \Mike42\Escpos\PrintConnectors\NetworkPrintConnector(
                    $printerConfig->ip_address,
                    $printerConfig->port
                ),
                'usb' => new \Mike42\Escpos\PrintConnectors\FilePrintConnector($printerConfig->usb_path),
                default => throw new Exception('Unsupported printer connection type'),
            };
        } catch (Exception $e) {
            logger()->error('Error creating printer connector: ' . $e->getMessage());
            return null;
        }
    }

    private function convertPdfToImage($pdfPath, $order)
    {
        try {
            if (!file_exists($pdfPath)) {
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
            logger()->error('PDF to image conversion failed: ' . $e->getMessage());
            return null;
        }
    }

}
