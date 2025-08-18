# Delivery Fees Page

This document describes the new delivery fees page functionality that has been added to the application.

## Overview

The delivery fees page allows customers to view delivery information for different regions and townships for each seller. This helps customers understand delivery costs and options before placing orders.

## Features

-   **Region Selection**: Users can select from available regions where the seller provides delivery
-   **Township Display**: Shows all townships within a selected region with their respective delivery fees
-   **Delivery Information**: Displays:
    -   Delivery fee for each township
    -   Cash on Delivery (COD) availability
    -   Pickup availability
    -   Additional remarks if any

## Files Added/Modified

### 1. Livewire Component

-   **File**: `app/Livewire/DeliveryFees.php`
-   **Purpose**: Handles the logic for displaying delivery information by regions and townships

### 2. Blade View

-   **File**: `resources/views/livewire/delivery-fees.blade.php`
-   **Purpose**: Displays the delivery fees interface with region selection and township details

### 3. Routes

-   **File**: `routes/tenant.php`
-   **Added**: `Route::get('delivery', \App\Livewire\DeliveryFees::class)->name('delivery');`

### 4. Navigation

-   **File**: `resources/views/livewire/layout/navigation.blade.php`
-   **Modified**: Added delivery navigation link to both desktop and mobile menus

### 5. Language Files

-   **Files**:
    -   `lang/en/messages.php`
    -   `lang/my/messages.php`
-   **Added**: Complete translation support for delivery-related content

### 6. Model Relationships

-   **File**: `app/Models/Township.php`
-   **Added**: `deliveries()` relationship method

## Usage

1. Navigate to the delivery page via the navigation menu
2. Select a region from the left sidebar
3. View township delivery information in the main content area
4. See delivery fees, COD availability, and pickup options for each township

## Database Requirements

The page relies on existing database tables:

-   `deliveries` - Contains delivery fees and options per seller/region/township
-   `regions` - Geographic regions
-   `townships` - Townships within regions
-   `sellers` - Seller information

## Multi-language Support

The page supports both English and Myanmar languages with complete translations for:

-   Page titles and headers
-   Table columns
-   Status indicators
-   Help text and notes
-   Navigation labels

## Responsive Design

The page is fully responsive and includes:

-   Desktop layout with sidebar region selection
-   Mobile-friendly responsive design
-   Proper spacing and typography
-   Accessibility features

## Notes

-   Only regions with delivery options for the current seller are displayed
-   The page shows "no delivery areas" message when no deliveries are configured
-   COD and pickup availability are clearly indicated with colored badges
-   Additional information notes are provided to help users understand delivery terms
