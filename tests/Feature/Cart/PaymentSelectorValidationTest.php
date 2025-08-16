<?php

declare(strict_types=1);

use App\Livewire\Cart\PaymentSelector;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Seller;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->seller = Seller::factory()->create();
    $this->payment = Payment::factory()->create([
        'seller_id' => $this->seller->id,
        'is_published' => true,
    ]);
    $this->paymentAccount = PaymentAccount::factory()->create([
        'payment_id' => $this->payment->id,
    ]);
});

test('payment selector displays error when payment slip is required but not uploaded', function () {
    $component = Livewire::actingAs($this->user)
        ->test(PaymentSelector::class)
        ->set('selectedPayment', $this->payment->id)
        ->set('selectedPaymentAccount', $this->paymentAccount->id)
        ->set('isCod', false);

    // Try to validate without payment slip
    $component->call('validateBeforeCheckout');

    $component->assertHasErrors(['paymentSlip']);
});

test('payment selector does not show error when COD is selected', function () {
    $component = Livewire::actingAs($this->user)
        ->test(PaymentSelector::class)
        ->set('isCod', true);

    $component->call('validateBeforeCheckout');

    $component->assertHasNoErrors();
});

test('payment selector validates file upload correctly', function () {
    $component = Livewire::actingAs($this->user)
        ->test(PaymentSelector::class)
        ->set('selectedPayment', $this->payment->id)
        ->set('selectedPaymentAccount', $this->paymentAccount->id)
        ->set('isCod', false);

    // Create a fake image file
    $file = \Livewire\Features\SupportFileUploads\TemporaryUploadedFile::fake()
        ->image('payment-slip.jpg');

    $component->set('paymentSlip', $file);

    $component->assertHasNoErrors(['paymentSlip']);
});

test('payment selector shows validation error for invalid file type', function () {
    $component = Livewire::actingAs($this->user)
        ->test(PaymentSelector::class)
        ->set('selectedPayment', $this->payment->id)
        ->set('selectedPaymentAccount', $this->paymentAccount->id)
        ->set('isCod', false);

    // Create a fake non-image file
    $file = \Livewire\Features\SupportFileUploads\TemporaryUploadedFile::fake()
        ->create('document.pdf', 100);

    $component->set('paymentSlip', $file);

    $component->assertHasErrors(['paymentSlip']);
});
