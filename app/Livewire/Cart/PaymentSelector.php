<?php

namespace App\Livewire\Cart;

use App\Helper\UserHelper;
use App\Models\Payment;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class PaymentSelector extends Component
{
    use WithFileUploads;

    #[Locked]
    public $sellerId;

    public $selectedPayment = '';

    public $selectedPaymentAccount = '';

    public $paymentSlip;

    public $isCod = false;

    public $codAvailable = false;

    public function mount(): void
    {
        $this->sellerId = UserHelper::getUserSellerId();
    }

    #[On('delivery-updated')]
    public function updateDeliveryDetails($details): void
    {
        // Update COD availability based on delivery details
        if (isset($details['unavailable']) && $details['unavailable']) {
            $this->isCod = false;
            $this->codAvailable = false;
        } else {
            $this->codAvailable = isset($details['is_cod']) ? $details['is_cod'] : false;

            // If COD is not available and user had it selected, uncheck it
            if (! $this->codAvailable && $this->isCod) {
                $this->isCod = false;
            }
        }

        $this->dispatch('cod-availability-updated', available: $this->codAvailable);
    }

    public function updatedIsCod(): void
    {
        if ($this->isCod) {
            $this->selectedPayment = '';
            $this->selectedPaymentAccount = '';
            $this->paymentSlip = null;
        }

        $this->dispatch('cod-updated', isCod: $this->isCod);
    }

    #[Computed]
    public function payments()
    {
        return Payment::where('seller_id', $this->sellerId)
            ->where('is_published', true)
            ->with('accounts')
            ->get();
    }

    #[Computed]
    public function paymentAccounts()
    {
        if (! $this->selectedPayment) {
            return collect();
        }

        $payment = $this->payments->find($this->selectedPayment);

        return $payment ? $payment->accounts : collect();
    }

    public function updatedSelectedPayment(): void
    {
        $this->selectedPaymentAccount = '';
        $this->dispatch('payment-updated', paymentId: $this->selectedPayment);
    }

    public function updatedSelectedPaymentAccount(): void
    {
        $this->dispatch('payment-account-updated', accountId: $this->selectedPaymentAccount);

        // Clear any previous payment slip errors when changing account
        $this->resetErrorBag('paymentSlip');
    }

    public function updatedPaymentSlip(): void
    {
        if ($this->paymentSlip) {
            // Validate the file immediately
            $this->validateOnly('paymentSlip', [
                'paymentSlip' => 'required|image|max:1024', // 1MB Max
            ]);

            // Store the file temporarily and save the path in session
            $path = $this->paymentSlip->store('temp/payment_slips', 'public');
            session(['payment_slip_temp_path' => $path]);
            $this->dispatch('payment-slip-updated', slip: $path);
        }
    }

    #[On('validate-payment-slip')]
    public function validatePaymentSlip(): array
    {
        if (! $this->isCod && $this->selectedPaymentAccount) {
            $this->validate([
                'paymentSlip' => 'required|image|max:1024', // 1MB Max
            ], [
                'paymentSlip.required' => __('messages.cart.validation.payment_slip_required'),
                'paymentSlip.image' => 'Payment slip must be an image file.',
                'paymentSlip.max' => 'Payment slip must not be larger than 1MB.',
            ]);

            return ['valid' => true];
        }

        return ['valid' => true];
    }

    public function validateBeforeCheckout(): bool
    {
        if (! $this->isCod && $this->selectedPaymentAccount && ! $this->paymentSlip) {
            $this->addError('paymentSlip', __('messages.cart.validation.payment_slip_required'));

            return false;
        }

        return true;
    }

    public function render()
    {
        return view('livewire.cart.payment-selector');
    }
}
