<?php

namespace App\Livewire\Seller;

use App\Models\SellerSubscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionPlans extends Component implements HasForms
{
    use InteractsWithForms;

    public $selectedPlan = null;

    public $showPaymentForm = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'kpay' => 'KPay',
                        'aya_pay' => 'Aya Pay',
                    ])
                    ->descriptions([
                        'kpay' => '09453283072',
                        'aya_pay' => '09777126169',
                    ])
                    ->required(),

                SpatieMediaLibraryFileUpload::make('payment_slip')
                    ->label('Payment Slip')
                    ->image()
                    ->required()
                    ->maxSize(5120) // 5MB
                    ->directory('subscription-payments'),

                Textarea::make('notes')->nullable()
                    ->label('Additional Notes')
                    ->placeholder('Any additional information about your payment'),
            ])->statePath('data');
    }

    public function selectPlan($planId)
    {
        $this->selectedPlan = SubscriptionPlan::findOrFail($planId);
        $this->showPaymentForm = true;
    }

    public $showThankYouPage = false;

    public function submitPayment()
    {
        $data = $this->form->getState();

        $seller = Auth::guard('seller')->user();

        // Check if seller already has a pending or processing subscription
        $existingSubscription = SellerSubscription::where('seller_id', $seller->id)
            ->whereIn('payment_status', ['pending', 'processing'])
            ->first();

        if ($existingSubscription) {
            Notification::make()
                ->title('Subscription Already Pending')
                ->body('You already have a pending subscription payment. Please wait for approval before submitting a new payment.')
                ->warning()
                ->send();

            return;
        }

        // Create a new subscription record
        $subscription = new SellerSubscription;
        $subscription->seller_id = $seller->id;
        $subscription->subscription_plan_id = $this->selectedPlan->id;
        $subscription->started_at = Carbon::now();
        $subscription->expired_at = Carbon::now()->addMonths($this->selectedPlan->duration_months);
        $subscription->price_paid = $this->selectedPlan->price;
        $subscription->payment_status = 'pending';
        $subscription->payment_method = $data['payment_method'];
        $subscription->notes = $data['notes'] ?? null;
        $subscription->save();

        Notification::make()
            ->title('Subscription Payment Submitted')
            ->body('Your payment has been submitted and is pending approval.')
            ->success()
            ->send();

        // Show thank you page instead of redirecting
        $this->showPaymentForm = false;
        $this->showThankYouPage = true;
    }

    public function render()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('livewire.seller.subscription-plans', [
            'plans' => $plans,
        ])->layout('layouts.guest');
    }
}
