# Cart Component Refactoring

This document outlines the refactoring of the monolithic Cart component into smaller, more manageable Livewire components.

## Overview

The original Cart component was a complex, single component handling multiple responsibilities:

-   Cart item display and management
-   Order summary calculations
-   Delivery selection
-   Address selection
-   Payment method selection
-   Order checkout process
-   Complex Alpine.js state management

## Refactored Structure

The cart functionality has been split into 6 focused components:

### 1. CartItems (`app/Livewire/Cart/CartItems.php`)

**Responsibility**: Managing cart items display, quantity updates, and item removal

-   `loadCartItems()` - Loads cart items from database
-   `removeItem($cartItemId)` - Removes item from cart
-   `updateQuantity($cartItemId, $quantity)` - Updates item quantity with validation
-   Dispatches `cart-updated` event when items change

### 2. CartSummary (`app/Livewire/Cart/CartSummary.php`)

**Responsibility**: Calculating and displaying order totals, points, delivery fees

-   `subtotal` computed property - Calculates cart subtotal
-   `total` computed property - Calculates final total including delivery
-   `pointsToEarn` computed property - Calculates points user will earn
-   Listens for `cart-updated` and `delivery-fee-updated` events

### 3. DeliverySelector (`app/Livewire/Cart/DeliverySelector.php`)

**Responsibility**: Handling delivery region/township selection and fees

-   `regions` computed property - Available regions for seller
-   `deliveries` computed property - Grouped delivery options
-   `townships` computed property - Townships for selected region
-   `updatedSelectedRegion()` - Handles region changes
-   `updatedSelectedTownship()` - Handles township changes and fee calculation
-   Dispatches `delivery-fee-updated` and `delivery-updated` events

### 4. AddressSelector (`app/Livewire/Cart/AddressSelector.php`)

**Responsibility**: Managing user address selection

-   `addresses` computed property - User's saved addresses
-   `updatedSelectedAddress()` - Handles address selection
-   Dispatches `address-updated` event

### 5. PaymentSelector (`app/Livewire/Cart/PaymentSelector.php`)

**Responsibility**: Payment method and account selection, payment slip upload

-   `payments` computed property - Available payment methods
-   `paymentAccounts` computed property - Accounts for selected payment method
-   `updatedSelectedPayment()` - Handles payment method changes
-   `updatedSelectedPaymentAccount()` - Handles payment account selection
-   `updatedPaymentSlip()` - Handles payment slip upload
-   Listens for `cod-updated` event
-   Dispatches payment-related events

### 6. CheckoutForm (`app/Livewire/Cart/CheckoutForm.php`)

**Responsibility**: Final checkout process and order creation

-   `checkout()` - Validates and processes the order
-   Listens for all component events to collect checkout data
-   Handles order creation, cart clearing, and redirection

### 7. Cart (`app/Livewire/Cart.php`)

**Responsibility**: Main container component (simplified)

-   Renders the main cart layout
-   Includes all sub-components
-   Listens for `cart-updated` event

## Component Communication

Components communicate using Livewire events:

```
CartItems → cart-updated → CartSummary, Cart
DeliverySelector → delivery-fee-updated → CartSummary
DeliverySelector → delivery-updated → CheckoutForm
DeliverySelector → cod-updated → PaymentSelector, CheckoutForm
AddressSelector → address-updated → CheckoutForm
PaymentSelector → payment-updated → CheckoutForm
PaymentSelector → payment-account-updated → CheckoutForm
PaymentSelector → payment-slip-updated → CheckoutForm
```

## Benefits of Refactoring

1. **Single Responsibility**: Each component has a focused purpose
2. **Easier Testing**: Smaller components are easier to unit test
3. **Better Maintainability**: Changes to one feature don't affect others
4. **Code Reusability**: Components can be reused in other contexts
5. **Improved Readability**: Smaller, focused code is easier to understand
6. **Simplified Debugging**: Issues are isolated to specific components
7. **No More Alpine.js Complexity**: Replaced complex Alpine.js with simple Livewire events

## File Structure

```
app/Livewire/
├── Cart.php (simplified main component)
└── Cart/
    ├── CartItems.php
    ├── CartSummary.php
    ├── DeliverySelector.php
    ├── AddressSelector.php
    ├── PaymentSelector.php
    └── CheckoutForm.php

resources/views/livewire/
├── cart.blade.php (simplified main view)
├── cart-original.blade.php (backup of original)
└── cart/
    ├── cart-items.blade.php
    ├── cart-summary.blade.php
    ├── delivery-selector.blade.php
    ├── address-selector.blade.php
    ├── payment-selector.blade.php
    └── checkout-form.blade.php
```

## Migration Notes

-   Original complex Alpine.js logic replaced with simple Livewire events
-   All functionality preserved
-   Improved error handling and validation
-   Better user experience with loading states
-   Removed ~500 lines of complex JavaScript/Alpine.js code

## Future Improvements

1. Add caching for delivery options
2. Implement real-time stock validation
3. Add cart persistence across sessions
4. Implement cart sharing functionality
5. Add cart analytics and tracking
