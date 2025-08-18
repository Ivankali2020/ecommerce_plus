<?php

declare(strict_types=1);

use App\Livewire\Order;
use App\Models\Order as OrderModel;
use App\Models\Seller;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->seller = Seller::factory()->create();
});

test('order component renders successfully', function () {
    $this->actingAs($this->user)
        ->get(route('orders'))
        ->assertStatus(200)
        ->assertSeeLivewire(Order::class);
});

test('order component displays orders without grouping', function () {
    // Create multiple orders for the user
    $order1 = OrderModel::factory()->create([
        'user_id' => $this->user->id,
        'seller_id' => $this->seller->id,
        'invoice_number' => 'ORDER001',
        'total_price' => 10000,
    ]);

    $order2 = OrderModel::factory()->create([
        'user_id' => $this->user->id,
        'seller_id' => $this->seller->id,
        'invoice_number' => 'ORDER002',
        'total_price' => 15000,
    ]);

    Livewire::actingAs($this->user)
        ->test(Order::class)
        ->assertSee('ORDER001')
        ->assertSee('ORDER002')
        ->assertSee('10,000')
        ->assertSee('15,000')
        ->assertDontSee($this->seller->name); // Should not see seller name as a header since we removed grouping
});

test('order component shows seller name in order info', function () {
    $order = OrderModel::factory()->create([
        'user_id' => $this->user->id,
        'seller_id' => $this->seller->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('orders'))
        ->assertSee($this->seller->name); // Should see seller name in the order details
});

test('order component computed property returns correct orders', function () {
    $order = OrderModel::factory()->create([
        'user_id' => $this->user->id,
        'seller_id' => $this->seller->id,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(Order::class);

    // Test that computed property returns paginated results
    expect($component->get('orders'))->toBeInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class);
    expect($component->get('orders')->count())->toBe(1);
});

test('order component displays empty state when no orders', function () {
    Livewire::actingAs($this->user)
        ->test(Order::class)
        ->assertSee(__('messages.orders.no_orders'))
        ->assertSee(__('messages.orders.start_shopping_message'));
});

test('order component shows order status correctly', function () {
    $order = OrderModel::factory()->create([
        'user_id' => $this->user->id,
        'seller_id' => $this->seller->id,
        'status' => \App\Helper\OrderStatus::New,
    ]);

    Livewire::actingAs($this->user)
        ->test(Order::class)
        ->assertSee(__('messages.orders.status.new'));
});
