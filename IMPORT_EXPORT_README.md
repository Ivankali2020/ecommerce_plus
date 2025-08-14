# Product Import/Export Documentation

This document explains how to use the Excel-based import/export functionality for products and variants using the `maatwebsite/excel` package.

## Features

-   Export existing products and variants to Excel
-   Import products and variants from Excel files
-   Download templates for easy import formatting
-   Support for products with variants (has_variant = true/false)
-   Validation and error handling

## How to Use

### 1. Export Products

Click "Export Products" button to download all products in Excel format. This includes:

-   Product details (name, SKU, description, etc.)
-   Category, brand, supplier information
-   Pricing in multiple currencies
-   Stock quantities
-   Boolean flags (is_public, is_recommended, etc.)
-   Variant count

### 2. Export Variants

Click "Export Variants" button to download all product variants in Excel format. This includes:

-   Variant details (name, SKU, pricing, etc.)
-   Parent product information
-   Attributes in encoded format
-   Stock quantities

### 3. Download Templates

Before importing, download templates to see the exact format required:

-   **Product Template**: Shows the required columns and sample data for products
-   **Variant Template**: Shows the required columns and sample data for variants

### 4. Import Products

1. Download the product template first
2. Fill in your product data following the template format
3. Click "Import Products" and select your Excel file
4. The system will validate and import your products

### 5. Import Variants

1. Make sure parent products exist in the system first
2. Download the variant template
3. Fill in variant data with correct product IDs
4. Click "Import Variants" and select your Excel file

## Excel Format Details

### Product Import Format

Required columns:

-   `name`: Product name (required)
-   `category`: Category name (must exist in system)
-   `currency`: Currency code (e.g., USD, MMK)
-   `price`: Product price
-   `quantity`: Stock quantity

Optional columns include: sku, description, brand, supplier, buy_price, mmk_price, etc.

### Variant Import Format

Required columns:

-   `product_id`: ID of parent product (required)
-   `variant_name`: Name of the variant (required)
-   `price`: Variant price (required)
-   `quantity`: Stock quantity (required)

### Boolean Values

Use these values for Yes/No fields:

-   **Yes**: "Yes", "1", 1, true
-   **No**: "No", "0", 0, false

### Attributes Format (for variants)

Attributes are encoded as: `attribute_id:value_id1,value_id2;attribute_id2:value_id3`

Example: `1:1,2;2:3,4` means:

-   Attribute 1 has values 1 and 2
-   Attribute 2 has values 3 and 4

## Error Handling

The system includes validation and error handling:

-   Invalid data will be skipped with notifications
-   Missing required relationships (category, exchange) will cause rows to be skipped
-   Import errors are displayed with specific error messages
-   Success notifications confirm successful imports

## Files Created

The implementation includes these new files:

-   `app/Exports/ProductExport.php` - Export products
-   `app/Exports/VariantExport.php` - Export variants
-   `app/Exports/ProductTemplateExport.php` - Product import template
-   `app/Exports/VariantTemplateExport.php` - Variant import template
-   `app/Imports/ProductImport.php` - Import products
-   `app/Imports/VariantImport.php` - Import variants
-   `app/Http/Controllers/ProductImportExportController.php` - Web routes controller

## Notes

1. The `maatwebsite/excel` package was already installed in your project
2. Routes are added to `routes/web.php` for web-based import/export
3. Filament actions are added to the ProductResource ListProducts page
4. The implementation is tenant-aware using the tenancy system
5. All imports respect existing relationships and validation rules
6. Products with variants (`has_variant = true`) can have multiple variants imported separately
