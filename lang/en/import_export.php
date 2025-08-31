<?php

return [
    'actions' => [
        'download_product_template' => 'Download Product Template',
        'download_variant_template' => 'Download Variant Template',
        'export_products' => 'Export Products',
        'export_variants' => 'Export Variants',
        'import_products' => 'Import Products',
        'import_variants' => 'Import Variants',
        'actions_group' => 'Actions',
    ],

    'form' => [
        'select_excel_file' => 'Select Excel File',
        'download_template_first' => 'Download the template first to see the required format.',
        'ensure_products_exist' => 'Download the template first to see the required format. Make sure products exist before importing variants.',
    ],

    'notifications' => [
        'products_imported_success' => 'Products imported successfully!',
        'variants_imported_success' => 'Variants imported successfully!',
        'error_importing_products' => 'Error importing products',
        'error_importing_variants' => 'Error importing variants',
        'import_queued' => 'Import queued successfully',
        'import_processing_message' => 'Your import is being processed in the background. You will receive a notification when complete.',
        'error_queuing_import' => 'Error queuing import',
    ],

    'file_names' => [
        'product_template' => 'product_import_template.xlsx',
        'variant_template' => 'variant_import_template.xlsx',
        'products_export' => 'products_:timestamp.xlsx',
        'variants_export' => 'variants_:timestamp.xlsx',
    ],
];
