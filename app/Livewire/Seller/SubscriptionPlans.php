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

    public ?SellerSubscription $subscription = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('payment_method')
                    ->label(__('subscription.payment_method'))
                    ->options([
                        'kpay' => 'KPay',
                    ])
                    ->descriptions([
                        'kpay' => '09 453183072 ( AUNG PYAE PHYO )',
                    ])
                    ->required(),

                SpatieMediaLibraryFileUpload::make('payment_slips')
                    ->label(__('subscription.payment_slip'))
                    ->image()
                    ->required()
                    ->maxSize(5120) // 5MB
                    ->directory('subscription-payments')
                    ->collection('payment_slips')
                    ->multiple(),

                Textarea::make('notes')->nullable()
                    ->label(__('subscription.additional_notes'))
                    ->placeholder(__('subscription.additional_notes_placeholder')),
            ])->statePath('data');
    }

    public function selectPlan($planId)
    {
        $this->selectedPlan = SubscriptionPlan::where('id',$planId)->where('is_active', true)->firstOrFail();

        // Create a new subscription instance for the form
        $this->subscription = new SellerSubscription();

        // Set the form model to the subscription instance
        $this->form->model($this->subscription);

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

        // Fill the subscription with data
        $this->subscription->fill([
            'seller_id' => $seller->id,
            'subscription_plan_id' => $this->selectedPlan->id,
            'started_at' => Carbon::now(),
            'expired_at' => Carbon::now()->addMonths($this->selectedPlan->duration_months),
            'price_paid' => $this->selectedPlan->price,
            'payment_status' => 'pending',
            'payment_method' => $data['payment_method'],
            'notes' => $data['notes'] ?? null,
        ]);

        $this->subscription->save();

        // Save the relationships (including media)
        $this->form->model($this->subscription)->saveRelationships();

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
