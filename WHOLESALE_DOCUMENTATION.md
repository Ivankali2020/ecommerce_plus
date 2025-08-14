# WholeSale Feature Documentation

## Overview

The WholeSale feature allows customers to view and filter products and variants that are marked for wholesale purchase. This feature provides advanced filtering capabilities and a user-friendly interface optimized for bulk purchasing.

## Features

### 1. Rich Filtering System

-   **Search**: Search by product name, description, or SKU
-   **Category Filter**: Filter by product categories
-   **Brand Filter**: Filter by product brands
-   **Price Range**: Set minimum and maximum price filters
-   **Stock Availability**: Toggle to show only items in stock
-   **Variants Display**: Toggle to show/hide product variants

### 2. Sorting Options

-   Sort by Name (A-Z, Z-A)
-   Sort by Price (Low to High, High to Low)
-   Sort by Date Added (Newest, Oldest)

### 3. Statistics Dashboard

-   Total wholesale products count
-   Total wholesale variants count
-   Price statistics (min, max, average)

### 4. Responsive Design

-   Desktop: Full sidebar with filters
-   Mobile: Collapsible filter panel
-   Touch-friendly interface

## Usage

### For Customers

1. **Access Wholesale Page**

    - Navigate to `/wholesale` on any tenant domain
    - Or use the "Wholesale" link in the navigation menu

2. **Using Filters**

    - Use the search box to find specific products
    - Select categories or brands from the filter sidebar
    - Set price ranges using min/max inputs
    - Toggle options for stock availability and variant display

3. **Sorting Results**

    - Use the sort buttons to organize results by name, price, or date

4. **Adding to Cart**
    - Click "Add to Cart" for products
    - Products with variants will use the first available variant
    - Variants can be added directly to cart
    - MOQ (Minimum Order Quantity) requirements are enforced

### For Sellers

1. **Mark Products as Wholesale**

    - In the Filament admin panel, edit products
    - Toggle the "Is Wholesale" option to true
    - Save the product

2. **Mark Variants as Wholesale**
    - Edit product variants in the admin panel
    - Toggle the "Is Wholesale" option for individual variants
    - This allows selective wholesale availability

## Technical Implementation

### Routes

-   `GET /wholesale` - Main wholesale page (requires tenant context)

### Livewire Component

-   `App\Livewire\WholeSale` - Main component handling all functionality

### Database Fields

-   `products.is_whole_sale` - Boolean flag for wholesale products
-   `variants.is_whole_sale` - Boolean flag for wholesale variants

### Language Support

-   Supports both English and Myanmar languages
-   All text strings are properly localized
-   Responsive font handling for Myanmar text

### Permissions

-   Accessible to all authenticated and unauthenticated users
-   Products must be marked as `is_public = true` to appear
-   Respects tenant scoping (only shows current tenant's products)

## Cart Integration

-   Fully integrated with existing cart system
-   Supports both products and variants
-   Enforces MOQ requirements
-   Shows stock availability
-   Real-time cart updates

## Mobile Features

-   Bottom navigation includes wholesale link
-   Collapsible filter sidebar
-   Touch-optimized interface
-   Load more functionality for pagination

## Customization

-   Easy to modify filters in the Livewire component
-   Customizable view templates
-   Language files can be extended
-   Styling can be adjusted in the Blade template

## Future Enhancements

-   Bulk pricing tiers
-   Wholesale-specific discounts
-   Export functionality for wholesale orders
-   Advanced reporting for wholesale sales
