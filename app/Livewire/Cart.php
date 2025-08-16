<?php

namespace App\Livewire;

use App\Helper\UserHelper;
use App\Models\Cart as CartModel;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Seller;
use App\Models\UserAddress;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class Cart extends Component
{
    use WithFileUploads;

    #[Locked]
    public $cartItems;

    public $regions;
    // public $deliveries;

    public $deliveryDetails;

    public $paymentDetails;

    public $townships = [];

    public $addresses;

    public $selectedRegion;

    public $selectedTownship;

    public $selectedAddress;

    public $deliveryFee = 0;

    public $is_cod = false;

    public string $note = '';

    // public $payments;
    public $selectedPayment;

    public $selectedPaymentAccount;

    public $paymentSlip;

    public $delivery_id;

    public $address_id = 0;

    public $payment_account_id;

    public $seller_id;

    public function mount()
    {
        if (! Auth::check('web')) {
            return redirect()->route('login');
        }

        $this->seller_id = UserHelper::getUserSellerId();
        $this->cartItems = CartModel::with(['product', 'variant'])
            ->where('user_id', auth('web')->id())
            ->where('seller_id', $this->seller_id)
            ->get();

        // $this->deliveries = self::getDeliveriresData();

        $this->regions = Seller::find($this->seller_id)->regions;
        $this->addresses = UserAddress::where('user_id', auth('web')->id())->get();
        // $this->payments = self::getPaymentsData();

    }

    #[Computed]
    public function payments()
    {
        return Payment::where('seller_id', $this->seller_id)->with('accounts')->get();
    }

    #[Computed]
    public function deliveries()
    {
        return Delivery::where('seller_id', $this->seller_id)
            ->with(['region', 'township'])->get()->groupBy(function ($item) {
                return $item->region->mm_name;
            })->map(function ($items, $name) {
                return [
                    'region_name' => $name,
                    'townships' => $items->map(function ($item) {
                        return [
                            'name' => $item->township->mm_name,
                            'township_id' => $item->township_id,
                            'region_id' => $item->region_id,
                            'delivery_id' => $item->id,
                            'fees' => $item->fees,
                            'is_cod' => $item->is_cod,
                            'is_pick_up' => $item->is_pick_up,
                            'remark' => $item->remark ?? '',
                        ];
                    }),
                ];
            })->values();
    }

    #[Computed]
    public function pointsToEarn()
    {
        if (! Auth::check()) {
            return 0;
        }

        $subtotal = $this->cartItems->sum(fn ($item) => $item->total);

        // Get points earning percentage from system settings (default to 1% if not set)
        $pointsPercentage = Seller::find($this->seller_id)->setting->data['point_percentage'];

        return floor($subtotal * ((int) $pointsPercentage / 100));
    }

    public function updatedSelectedRegion()
    {
        if ($this->selectedRegion == null) {
            $this->townships = [];

            return;
        }
        $this->townships = Delivery::where('seller_id', $this->seller_id)->where('region_id', $this->selectedRegion)->get();
        $this->selectedTownship = null;
        $this->deliveryFee = 0;
    }

    public function updatedSelectedTownship($townshipId)
    {
        $township = Delivery::where('seller_id', $this->seller_id)->where('township_id', $townshipId)->first();
        $this->deliveryFee = $township ? $township->fees : 0;
    }

    public function removeItem($cartItemId)
    {

        CartModel::find($cartItemId)->delete();
        $this->cartItems = CartModel::with(['product', 'variant'])
            ->where('seller_id', UserHelper::getUserSellerId())
            ->where('user_id', auth('web')->id())
            ->get();
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        $cart = CartModel::where('id', $cartItemId)
            ->where('seller_id', UserHelper::getUserSellerId())
            ->with(['product', 'variant'])->first();

        // Check if quantity is valid
        if ($quantity <= 0) {
            return;
        }

        // checking wholesale MOQ when decreasing quantity
        if ($cart->product->is_whole_sale && $quantity < $cart->product->MOQ) {
            Notification::make()
                ->title(__('messages.cart.wholesale_moq_title'))
                ->body(__('messages.cart.wholesale_moq_message', ['moq' => $cart->product->MOQ]))
                ->warning()
                ->send();

            return;
        }

        // Check available stock
        $availableStock = $cart->variant ? $cart->variant->quantity : $cart->product->quantity;

        if ($quantity > $availableStock) {
            Notification::make()
                ->title(__('messages.cart.out_of_stock'))
                ->body(__('messages.cart.limited_stock', ['quantity' => $availableStock]))
                ->warning()
                ->send();

            // Set quantity to maximum available
            $quantity = $availableStock;
        }

        // Update cart item
        CartModel::find($cartItemId)->update([
            'quantity' => $quantity,
            'total' => $cart->price * $quantity,
        ]);

        $this->cartItems = CartModel::with(['product', 'variant'])
            ->where('user_id', auth('web')->id())
            ->where('seller_id', $this->seller_id)
            ->get();
    }

    public function checkout()
    {

        $rules = [
            'delivery_id' => 'required|exists:deliveries,id',
            'address_id' => 'required|exists:user_addresses,id',
            'is_cod' => 'required|boolean',
        ];

        if (! $this->is_cod) {
            $rules['payment_account_id'] = 'required|exists:payment_accounts,id';
            $rules['paymentSlip'] = 'required|image|max:1024'; // 1MB Max
        }

        $this->validate($rules);

        DB::beginTransaction();

        try {

            $delivery = Delivery::where('id', $this->delivery_id)->first();

            $total = $this->cartItems->sum(fn ($item) => $item->total);

            $paymentAccount = PaymentAccount::find($this->payment_account_id);

            $order = Order::create([
                'invoice_number' => uniqid(),
                'user_id' => Auth::id(),
                'seller_id' => $this->seller_id,
                'tenant_id' => tenant('id'),
                'user_address_id' => $this->address_id,
                'delivery_fees' => $delivery->fees,
                'is_cod' => $this->is_cod,
                'payment_id' => $this->is_cod ? null : $paymentAccount->payment_id,
                'payment_account_id' => $this->is_cod ? null : $this->payment_account_id,
                'payment_slip' => $this->is_cod ? null : $this->paymentSlip->store('payment_slips', 'public'),
                'sub_total' => $total,
                'total_price' => $total + $delivery->fees,
                'note' => $this->note,
            ]);

            // Create order products from cart items
            foreach ($this->cartItems as $cartItem) {
                if ($cartItem->variant) {
                    if ($cartItem->quantity > $cartItem->variant->quantity) {
                        throw new Exception($cartItem->product->name.' stock is not enough');
                    }
                    // $cartItem->variant->decrement('quantity', $cartItem->quantity);

                } else {
                    if ($cartItem->quantity > $cartItem->product->quantity) {
                        throw new Exception($cartItem->product->name.' stock is not enough');
                    }
                    // $cartItem->decrement('quantity', $cartItem->quantity);
                }
                $order->products()->create([
                    'product_id' => $cartItem->product_id,
                    'variant_id' => $cartItem->variant_id,
                    'price' => $cartItem->price,
                    'quantity' => $cartItem->quantity,
                    'sub_price' => $cartItem->total,
                    'attributes' => $cartItem->variant ? $cartItem->variant->attributeValues : $cartItem->product->attributeValues,
                    'discount_price' => $cartItem->variant ? $cartItem->variant->mmk_discount_price : $cartItem->product->mmk_discount_price,
                    'discount' => $cartItem->variant ? $cartItem->variant->discount : $cartItem->product->discount,
                    'original_buy_price' => $cartItem->variant ? $cartItem->variant->buy_price : $cartItem->product->buy_price,
                    'original_price' => $cartItem->variant ? $cartItem->variant->price : $cartItem->product->price,
                    'original_discount_price' => $cartItem->variant ? $cartItem->variant->mmk_discount_price : $cartItem->product->mmk_discount_price,
                    'currency_id' => $cartItem->product->exchange->currency_id,
                    'exchange_rate' => $cartItem->product->exchange->rate,
                ]);
            }

            // Clear the cart
            $this->cartItems->each->delete();

            DB::commit();

            return redirect()->route('order.detail', $order->id);
        } catch (Exception $exception) {

            DB::rollback();
            // dd($exception->getMessage());
            Notification::make()->title('ERROR FOUND')->body($exception->getMessage())->danger()->send();
        }
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
