<?php

declare(strict_types=1);

use App\Filament\Seller\Resources\Orders\OrderResource;

test('discount percentage calculation from discount price works correctly', function () {
    $mockGet = new class
    {
        private array $data = [
            'price' => 100,
            'discount_price' => 80,
            'quantity' => 2,
            'products' => [],
        ];

        public function __invoke(string $key)
        {
            return $this->data[$key] ?? null;
        }
    };

    $mockSet = new class
    {
        public array $data = [];

        public function __invoke(string $key, $value): void
        {
            $this->data[$key] = $value;
        }
    };

    OrderResource::thisIsAutoChangeDiscountPercentage($mockGet, $mockSet);

    // 20% discount should be calculated: (100 - 80) / 100 * 100 = 20%
    expect($mockSet->data['discount'])->toBe(20.0);
    expect($mockSet->data['sub_price'])->toBe(160.0); // 80 * 2
});

test('discount percentage calculation when discount price equals original price', function () {
    $mockGet = new class
    {
        private array $data = [
            'price' => 100,
            'discount_price' => 100,
            'quantity' => 1,
            'products' => [],
        ];

        public function __invoke(string $key)
        {
            return $this->data[$key] ?? null;
        }
    };

    $mockSet = new class
    {
        public array $data = [];

        public function __invoke(string $key, $value): void
        {
            $this->data[$key] = $value;
        }
    };

    OrderResource::thisIsAutoChangeDiscountPercentage($mockGet, $mockSet);

    // No discount should be applied
    expect($mockSet->data['discount'])->toBe(0);
    expect($mockSet->data['discount_price'])->toBe(100);
    expect($mockSet->data['sub_price'])->toBe(100.0);
});

test('discount percentage calculation when discount price is zero', function () {
    $mockGet = new class
    {
        private array $data = [
            'price' => 100,
            'discount_price' => 0,
            'quantity' => 1,
            'products' => [],
        ];

        public function __invoke(string $key)
        {
            return $this->data[$key] ?? null;
        }
    };

    $mockSet = new class
    {
        public array $data = [];

        public function __invoke(string $key, $value): void
        {
            $this->data[$key] = $value;
        }
    };

    OrderResource::thisIsAutoChangeDiscountPercentage($mockGet, $mockSet);

    // 100% discount should be applied
    expect($mockSet->data['discount'])->toBe(100);
    expect($mockSet->data['sub_price'])->toBe(0);
});

test('change amount calculation works correctly', function () {
    $mockGet = new class
    {
        private array $data = [
            'given_amount' => 150,
            'total_price' => 100,
        ];

        public function __invoke(string $key)
        {
            return $this->data[$key] ?? null;
        }
    };

    $mockSet = new class
    {
        public array $data = [];

        public function __invoke(string $key, $value): void
        {
            $this->data[$key] = $value;
        }
    };

    OrderResource::calculateChangeAmount($mockGet, $mockSet);

    // Change should be 50
    expect($mockSet->data['change_amount'])->toBe(50.0);
});

test('change amount calculation when given amount is less than total', function () {
    $mockGet = new class
    {
        private array $data = [
            'given_amount' => 80,
            'total_price' => 100,
        ];

        public function __invoke(string $key)
        {
            return $this->data[$key] ?? null;
        }
    };

    $mockSet = new class
    {
        public array $data = [];

        public function __invoke(string $key, $value): void
        {
            $this->data[$key] = $value;
        }
    };

    OrderResource::calculateChangeAmount($mockGet, $mockSet);

    // Change amount should be 0 (cannot be negative)
    expect($mockSet->data['change_amount'])->toBe(0.0);
});
