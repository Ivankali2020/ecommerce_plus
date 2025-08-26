<?php

return [
    'navigation' => [
        'label' => 'Orders',
        'group' => 'Business Section',
        'plural' => 'Orders',
        'single' => 'Order',
    ],
    'form' => [
        'products' => [
            'label' => 'Products',
            'helper_text' => '* Calculated in Myanmar Kyat *',
            'details' => [
                'label' => 'Details Information',
                'buy_price' => 'Buy Price',
                'old_price' => 'Old Price',
                'old_discount_price' => 'Old Discount Price',
                'exchange_rate' => 'Exchange Rate',
                'currency' => 'Currency',
            ],
            'selection' => [
                'product' => 'Product',
                'quantity' => 'Quantity',
                'quantity_error' => ' only remaining',
                'variant' => 'Variant',
                'price' => 'Price',
                'discount' => 'Discount',
                'discount_price' => 'Discount Price',
                'sub_price' => 'Sub Price',
            ],
        ],
        'attributes' => [
            'label' => 'Attributes',
            'attribute' => 'Attribute',
            'value' => 'Value',
        ],
        'order_info' => [
            'label' => 'Order Information',
            'invoice_number' => 'Invoice Number',
            'seller' => 'Seller',
            'walk_in' => [
                'label' => 'Walk In',
                'helper_text' => 'Customer walk in type',
            ],
            'customer' => [
                'name' => 'Customer Name',
                'email' => 'Email',
                'phone' => 'Phone',
                'address' => 'Address',
                'region' => 'Region',
                'township' => 'Township',
            ],
            'cod' => [
                'label' => 'Cash on Delivery',
                'helper_text' => 'Order will be delivered and payment collected upon delivery',
            ],
            'payment' => 'Payment',
            'payment_account' => 'Payment Account',
            'status' => [
                'label' => 'Status',
                'options' => [
                    'new' => 'New',
                    'processing' => 'Processing',
                ],
            ],
            'purchase_date' => 'Purchase Date',
        ],
        'price_info' => [
            'label' => 'Price Information',
            'sub_total' => [
                'label' => 'Sub Total',
                'helper_text' => 'Product price total',
            ],
            'delivery_fees' => 'Delivery Fees',
            'tax' => [
                'label' => 'Tax',
                'helper_text' => 'Based on sub total',
                'amount' => 'Amount of tax',
            ],
            'total_price' => [
                'label' => 'Total Price',
                'helper_text' => 'All total price',
            ],
            'given_amount' => 'Given Amount',
            'change_amount' => 'Change Amount',
        ],
        'payment_slip' => 'Payment Slip',
    ],
    'table' => [
        'columns' => [
            'user' => 'Customer',
            'payment' => 'Payment',
            'address' => 'Address',
            'status' => 'Status',
            'tax_price' => 'Tax Amount',
            'sub_total' => 'Sub Total',
            'total_price' => 'Total Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
        'filters' => [
            'trashed' => 'Trashed',
        ],
        'actions' => [
            'invoice' => 'Invoice',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'restore' => 'Restore',
        ],
        'summary' => [
            'total_amount' => 'Total Amount',
        ],
    ],
];
