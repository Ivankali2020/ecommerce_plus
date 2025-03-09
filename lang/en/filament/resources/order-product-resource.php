<?php

return [
    'navigation' => [
        'label' => 'Order Products',
        'group' => 'Order Section',
        'plural' => 'Order Products',
        'single' => 'Order Product',
    ],
    'columns' => [
        'order' => [
            'invoice_number' => 'Invoice Number',
        ],
        'product' => [
            'name' => 'Product Name',
            'category' => 'Category',
        ],
        'attributes' => 'Attributes',
        'original_buy_price' => 'Buy Price',
        'original_price' => 'Original Price',
        'selling_price' => 'Selling Price',
        'discount_price' => 'Discount Price',
        'discount' => 'Discount',
        'quantity' => 'Quantity',
        'profit_loss' => 'Profit/Loss',
        'sub_price' => 'Sub Total',
        'total_amount' => 'Total Amount',
        'created_at' => 'Created Date',
    ],
    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'restore' => 'Restore',
    ],
];
