<?php

namespace App\Livewire\Cart;

use App\Helper\UserHelper;
use App\Models\Cart as CartModel;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\PaymentAccount;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class CheckoutForm extends Component
{
    #[Locked]
    public $sellerId;

    public string $note = '';

    // Data from other components
    public $deliveryId = null;

    public $addressId = null;

    public $paymentId = null;

    public $paymentAccountId = null;

    public $paymentSlip = null;

    public $isCod = false;

    public function mount(): void
    {
        $this->sellerId = UserHelper::getUserSellerId();
    }

    #[On('delivery-updated')]
    public function updateDelivery($details): void
    {
        $this->deliveryId = $details['delivery_id'] ?? null;
    }

    #[On('address-updated')]
    public function updateAddress($addressId): void
    {
        $this->addressId = $addressId;
    }

    #[On('payment-updated')]
    public function updatePayment($paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    #[On('payment-account-updated')]
    public function updatePaymentAccount($accountId): void
    {
        $this->paymentAccountId = $accountId;
    }

    #[On('cod-updated')]
    public function updateCodStatus($isCod): void
    {
        $this->isCod = $isCod;
    }

    #[On('payment-slip-updated')]
    public function updatePaymentSlip($slip): void
    {
        $this->paymentSlip = $slip; // Now this is a file path
    }

    protected function messages(): array
    {
        return [
            'deliveryId.required' => __('messages.cart.validation.delivery_required'),
            'deliveryId.exists' => __('messages.cart.validation.delivery_invalid'),
            'addressId.required' => __('messages.cart.validation.address_required'),
            'addressId.exists' => __('messages.cart.validation.address_invalid'),
            'isCod.required' => __('messages.cart.validation.payment_method_required'),
            'isCod.boolean' => __('messages.cart.validation.payment_method_invalid'),
            'paymentAccountId.required' => __('messages.cart.validation.payment_account_required'),
            'paymentAccountId.exists' => __('messages.cart.validation.payment_account_invalid'),
            'paymentSlip.required' => __('messages.cart.validation.payment_slip_required'),
            'paymentSlip.string' => __('messages.cart.validation.payment_slip_invalid'),
        ];
    }

    public function checkout()
    {
        $rules = [
            'deliveryId' => 'required|exists:deliveries,id',
            'addressId' => 'required|exists:user_addresses,id',
            'isCod' => 'required|boolean',
        ];

        if (! $this->isCod) {
            $rules['paymentAccountId'] = 'required|exists:payment_accounts,id';
            $rules['paymentSlip'] = 'required|string'; // Now it's a file path

            // Dispatch validation event to PaymentSelector component
            $this->dispatch('validate-payment-slip');
        }

        $this->validate($rules, $this->messages());

        DB::beginTransaction();

        try {
            $cartItems = CartModel::with(['product', 'variant'])
                ->where('user_id', auth('web')->id())
                ->where('seller_id', $this->sellerId)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new Exception('Cart is empty');
            }

            $delivery = Delivery::find($this->deliveryId);

            // check delivery is available for the seller
            if ($delivery->seller_id !== $this->sellerId) {
                throw new Exception(__('messages.cart.delivery_not_available'));
            }
            $total = $cartItems->sum(fn ($item) => $item->total);
            $paymentAccount = $this->paymentAccountId ? PaymentAccount::find($this->paymentAccountId) : null;

            // Move temp file to permanent location if not COD
            $finalPaymentSlipPath = null;
            if (! $this->isCod && $this->paymentSlip) {
                // Move from temp to permanent location
                $tempPath = 'public/'.$this->paymentSlip;
                $permanentPath = 'payment_slips/'.basename($this->paymentSlip);

                if (Storage::disk('public')->exists($this->paymentSlip)) {
                    Storage::disk('public')->move($this->paymentSlip, $permanentPath);
                    $finalPaymentSlipPath = $permanentPath;
                }
            }

            $order = Order::create([
                'invoice_number' => uniqid(),
                'user_id' => Auth::id(),
                'seller_id' => $this->sellerId,
                'tenant_id' => tenant('id'),
                'user_address_id' => $this->addressId,
                'delivery_fees' => $delivery->fees,
                'is_cod' => $this->isCod,
                'payment_id' => $this->isCod ? null : $paymentAccount?->payment_id,
                'payment_account_id' => $this->isCod ? null : $this->paymentAccountId,
                'payment_slip' => $finalPaymentSlipPath,
                'sub_total' => $total,
                'total_price' => $total + $delivery->fees,
                'note' => $this->note,
            ]);

            // Create order products from cart items
            foreach ($cartItems as $cartItem) {
                if ($cartItem->variant) {
                    if ($cartItem->quantity > $cartItem->variant->quantity) {
                        throw new Exception($cartItem->product->name.' stock is not enough');
                    }
                } else {
                    if ($cartItem->quantity > $cartItem->product->quantity) {
                        throw new Exception($cartItem->product->name.' stock is not enough');
                    }
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
            $cartItems->each->delete();

            // Clear payment slip session data
            session()->forget('payment_slip_temp_path');

            DB::commit();

            $this->redirect(route('order.detail', $order->id));
        } catch (Exception $exception) {
            DB::rollback();

            Notification::make()
                ->title('Error')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.cart.checkout-form');
    }
}
