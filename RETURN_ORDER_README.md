# Order Return System Documentation

## Overview

The Order Return system allows customers to request returns for products they've purchased, and enables sellers to manage these return requests efficiently. The system handles the complete return lifecycle from request creation to completion, including inventory management and refund processing.

## Table of Contents

1. [System Architecture](#system-architecture)
2. [Models & Relationships](#models--relationships)
3. [Return Status Flow](#return-status-flow)
4. [Features](#features)
5. [Observer Pattern](#observer-pattern)
6. [API Endpoints](#api-endpoints)
7. [Testing](#testing)
8. [Configuration](#configuration)

## System Architecture

### Directory Structure

```
app/
├── Filament/Seller/Resources/OrderReturns/
│   ├── OrderReturnResource.php
│   ├── Pages/
│   │   ├── CreateOrderReturn.php
│   │   ├── EditOrderReturn.php
│   │   ├── ListOrderReturns.php
│   │   └── ViewOrderReturn.php
│   ├── Schemas/
│   │   ├── OrderReturnForm.php
│   │   └── OrderReturnInfolist.php
│   └── Tables/
│       └── OrderReturnsTable.php
├── Models/
│   ├── OrderReturn.php
│   └── OrderReturnProduct.php
├── Observers/
│   └── OrderReturnProductObserver.php
└── Helper/
    └── ReturnStatus.php
```

## Models & Relationships

### OrderReturn Model

**Primary Fields:**
- `invoice_number` - Unique return identifier
- `status` - Current return status (enum)
- `total_refund_amount` - Total amount to be refunded
- `reason` - Customer's return reason
- `admin_notes` - Internal seller notes

**Relationships:**
- `belongsTo(Order::class)` - Parent order
- `belongsTo(User::class)` - Customer who initiated return
- `belongsTo(Seller::class)` - Seller processing return
- `hasMany(OrderReturnProduct::class)` - Individual returned products

### OrderReturnProduct Model

**Primary Fields:**
- `quantity_requested` - Quantity customer wants to return
- `quantity_approved` - Quantity seller approved for return
- `unit_refund_amount` - Refund amount per unit
- `total_refund_amount` - Total refund for this product
- `is_restocked` - Whether product should be restocked
- `condition_notes` - Notes about product condition

**Relationships:**
- `belongsTo(OrderReturn::class)` - Parent return request
- `belongsTo(OrderProduct::class)` - Original order product

## Return Status Flow

The return system uses the `ReturnStatus` enum with the following states:

```php
enum ReturnStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
```

### Status Transitions

1. **Pending** → Customer submits return request
2. **Under Review** → Seller begins reviewing request
3. **Approved/Rejected** → Seller makes decision
4. **Processing** → Return is being processed
5. **Completed** → Return finalized, refunds processed
6. **Cancelled** → Return cancelled by customer or seller

## Features

### 1. Bulk Return Creation

Sellers can create returns for multiple products from a single order:

```php
// Bulk action in OrderProductResource
BulkAction::make('bulk_return')
    ->schema([
        Repeater::make('return_items')
            ->schema([
                // Product selection and return details
            ])
    ])
```

**Features:**
- ✅ Automatic quantity validation
- ✅ Real-time refund amount calculation
- ✅ Bulk restocking decisions
- ✅ Individual product notes

### 2. Inventory Management

The system automatically handles inventory updates through the `OrderReturnProductObserver`:

**When `is_restocked = true`:**
- ✅ Increments product quantity
- ✅ Updates variant quantity (if applicable)
- ✅ Tracks restocking in order_products table

**When quantity changes:**
- ✅ Adjusts inventory by the difference
- ✅ Prevents over-restocking
- ✅ Maintains audit trail

### 3. Financial Tracking

**Order Product Updates:**
- `is_returned` - Boolean flag
- `returned_quantity` - Total returned quantity
- `total_refunded_amount` - Total refunded amount

**Calculations:**
- Automatically calculates totals across all return products
- Handles partial returns and multiple return requests
- Maintains financial integrity

### 4. Media Management

Returns support file attachments using Spatie Media Library:

```php
// Register media collections
public function registerMediaCollections(): void
{
    $this->addMediaCollection('return_images')
        ->acceptsMimeTypes(['image/jpeg', 'image/png'])
        ->singleFile();
        
    $this->addMediaCollection('return_documents')
        ->acceptsMimeTypes(['application/pdf'])
        ->singleFile();
}
```

### 5. Professional UI Components

**OrderReturnInfolist Features:**
- 📊 Organized sections with icons
- 💰 Currency formatting (`number_format($state, 2) . ' MMK'`)
- 🏷️ Status badges with color coding
- 📱 Responsive grid layouts
- 🔄 Return products repeater display

## Observer Pattern

### OrderReturnProductObserver

The observer handles automatic inventory and data updates:

#### Created Event
```php
public function created(OrderReturnProduct $orderReturnProduct): void
{
    if ($orderReturnProduct->is_restocked && 
        $orderReturnProduct->quantity_approved > 0 && 
        $this->isOrderReturnCompleted($orderReturnProduct)) {
        
        $this->handleRestocking($orderReturnProduct);
        $this->updateOrderProduct($orderReturnProduct);
    }
}
```

#### Updated Event
```php
public function updated(OrderReturnProduct $orderReturnProduct): void
{
    // Handle is_restocked changes
    if ($orderReturnProduct->wasChanged('is_restocked') && 
        $orderReturnProduct->is_restocked) {
        // Restock logic
    }
    
    // Handle quantity_approved changes
    if ($orderReturnProduct->wasChanged('quantity_approved')) {
        // Quantity adjustment logic
    }
}
```

#### Deleted Event
```php
public function deleted(OrderReturnProduct $orderReturnProduct): void
{
    // Reverse all inventory and order product updates
    $this->reverseOrderProductUpdates($orderReturnProduct);
}
```

## API Endpoints

### Return Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/seller/order-returns` | List all returns |
| POST | `/seller/order-returns` | Create new return |
| GET | `/seller/order-returns/{id}` | View return details |
| PUT | `/seller/order-returns/{id}` | Update return |
| DELETE | `/seller/order-returns/{id}` | Delete return |

### Bulk Operations

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/seller/order-products/bulk-return` | Create bulk return |

## Testing

### Feature Tests

```php
// Test return creation
test('seller can create order return', function () {
    $seller = Seller::factory()->create();
    $order = Order::factory()->create();
    
    $response = $this->actingAs($seller)
        ->post('/seller/order-returns', [
            'order_id' => $order->id,
            'reason' => 'Product defective',
            'total_refund_amount' => 100.00,
        ]);
        
    $response->assertCreated();
    expect(OrderReturn::count())->toBe(1);
});
```

### Observer Tests

```php
// Test inventory restocking
test('product quantity increases when return product is restocked', function () {
    $product = Product::factory()->create(['quantity' => 10]);
    $orderReturn = OrderReturn::factory()->create(['status' => ReturnStatus::Completed]);
    
    $returnProduct = OrderReturnProduct::create([
        'order_return_id' => $orderReturn->id,
        'quantity_approved' => 2,
        'is_restocked' => true,
    ]);
    
    expect($product->fresh()->quantity)->toBe(12);
});
```

### UI Tests

```php
// Test Filament interface
test('seller can view return details', function () {
    $return = OrderReturn::factory()->create();
    
    livewire(ViewOrderReturn::class, ['record' => $return])
        ->assertSuccessful()
        ->assertSee($return->invoice_number);
});
```

## Configuration

### Observer Registration

```php
// app/Providers/EventServiceProvider.php
protected $observers = [
    OrderReturnProduct::class => OrderReturnProductObserver::class,
];
```

## Best Practices

### 1. Status Management
- Always validate status transitions
- Use enum values consistently
- Log status changes for audit trail

### 2. Inventory Safety
- Validate quantity limits before restocking
- Handle concurrent updates properly
- Maintain inventory audit logs

### 3. Financial Integrity
- Calculate totals from database, not user input
- Validate refund amounts against original prices
- Track all financial transactions

### 4. User Experience
- Provide clear status indicators
- Show progress through return workflow
- Display helpful error messages

### 5. Performance
- Eager load relationships in lists
- Use database transactions for complex operations
- Implement proper caching where appropriate

## Security Considerations

1. **Authorization**: Ensure sellers can only access their own returns
2. **Validation**: Validate all input data, especially financial amounts
3. **Audit Trail**: Log all significant actions and changes
4. **Rate Limiting**: Prevent abuse of return creation endpoints

## Troubleshooting

### Common Issues

1. **Observer Not Firing**
   - Check observer registration in EventServiceProvider
   - Clear application cache: `php artisan cache:clear`
   - Verify model events are not disabled

2. **Inventory Not Updating**
   - Check `is_restocked` flag and return status
   - Verify product/variant relationships
   - Review observer logic conditions

3. **Total Calculations Wrong**
   - Ensure using database calculations, not cached values
   - Check for decimal precision issues
   - Verify all return products are included

### Debug Commands

```bash
# Check observer registration
php artisan tinker
>>> app('events')->getListeners('eloquent.created: App\Models\OrderReturnProduct')

# Test inventory updates
php artisan tinker
>>> $product = App\Models\Product::find(1)
>>> $product->quantity // Check current quantity

# Verify return calculations
php artisan tinker
>>> $return = App\Models\OrderReturn::find(1)
>>> $return->returnProducts->sum('total_refund_amount')
```

---

## Changelog

- **v1.0.0** - Initial return system implementation
- **v1.1.0** - Added bulk return functionality
- **v1.2.0** - Enhanced UI with professional components
- **v1.3.0** - Implemented comprehensive observer pattern

---

*Last updated: August 31, 2025*