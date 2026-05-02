<?php

use App\Filament\Seller\Resources\OrderReturns\OrderReturnResource;

beforeEach(function () {
    $this->originalLocale = app()->getLocale();
});

afterEach(function () {
    app()->setLocale($this->originalLocale);
});

test('order return resource uses english translations', function () {
    app()->setLocale('en');

    expect(OrderReturnResource::getNavigationLabel())->toBe('Order Returns');
    expect(OrderReturnResource::getModelLabel())->toBe('Order Return');
    expect(OrderReturnResource::getPluralModelLabel())->toBe('Order Returns');
    expect(__('filament/resources/order-return-resource.table.columns.invoice_number'))->toBe('Return Number');
});

test('order return resource uses myanmar translations', function () {
    app()->setLocale('my');

    expect(OrderReturnResource::getNavigationLabel())->toBe('အော်ဒါ ပြန်လည်ပေးအပ်မှုများ');
    expect(OrderReturnResource::getModelLabel())->toBe('အော်ဒါ ပြန်လည်ပေးအပ်မှု');
    expect(OrderReturnResource::getPluralModelLabel())->toBe('အော်ဒါ ပြန်လည်ပေးအပ်မှုများ');
    expect(__('filament/resources/order-return-resource.table.columns.invoice_number'))->toBe('ပြန်လည်ပေးအပ်မှု နံပါတ်');
});
