<?php

return [
    'navigation' => [
        'label' => 'Purchases',
        'group' => 'Business Section',
        'plural_label' => 'Purchases',
        'single_label' => 'Purchase',
    ],
    'form' => [
        'products' => [
            'label' => 'Products',
            'item' => [
                'product' => 'Product',
                'variant' => 'Variant',
                'stocks' => 'Stocks',
                'buy_price' => 'Buy Price',
                'sell_price' => 'Sell Price',
                'total' => 'Total',
                'attributes' => [
                    'label' => 'Attributes',
                    'attribute' => 'Attribute',
                    'value' => 'Value',
                    'select' => 'Select',
                ],
            ],
        ],
        'supplier_id' => 'Supplier',
        'status' => 'Status',
        'total_amount' => 'Total Amount',
        'paid_amount' => 'Paid Amount',
        'remaining_amount' => 'Remaining Amount',
    ],
    'table' => [
        'columns' => [
            'seller' => 'Seller',
            'supplier' => 'Supplier',
            'status' => 'Status',
            'total_amount' => 'Total Amount',
            'paid_amount' => 'Paid Amount',
            'remaining_amount' => 'Remaining Amount',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
        'actions' => [
            'invoice' => 'Invoice',
            'import_stock' => 'Import Stock',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ],
    ],
    'actions' => [
        'import_stock_success' => 'Stock imported successfully',
    ],
];
