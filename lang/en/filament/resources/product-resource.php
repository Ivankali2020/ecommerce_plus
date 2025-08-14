<?php

return [
    'navigation' => [
        'group' => 'Business Section',
        'label' => 'Products',
        'plural' => 'Products',
        'single' => 'Product',
    ],
    'form' => [
        'seller' => [
            'label' => 'Seller',
        ],
        'supplier' => [
            'label' => 'Supplier',
        ],
        'currency' => [
            'label' => 'Currency',
        ],
        'brand' => [
            'label' => 'Brand',
        ],
        'category' => [
            'label' => 'Category',
            'placeholder' => 'Please select a category',
        ],
        'exchange' => [
            'label' => 'Currency',
            'placeholder' => 'Please select a currency',
        ],
        'name' => [
            'label' => 'Name',
            'placeholder' => 'Product Name',
        ],
        'sku' => [
            'label' => 'SKU',
        ],
        'description' => [
            'label' => 'Description',
        ],
        'variants' => [
            'label' => 'Variants',
            'name' => [
                'label' => 'Name',
                'placeholder' => 'Product Name',
            ],
            'sku' => [
                'label' => 'SKU',
            ],
            'buy_price' => [
                'label' => 'Buy Price',
                'helper_text' => 'Calculate for profit and loss',
            ],
            'price' => [
                'label' => 'Price',
                'placeholder' => 'Resell price',
            ],
            'discount' => [
                'label' => 'Discount',
            ],
            'discount_price' => [
                'label' => 'Discount Price',
            ],
            'quantity' => [
                'label' => 'Quantity',
                'placeholder' => 'Your in stock amount',
            ],
            'moq' => [
                'label' => 'Min Amount Of Quantity (MOQ)',
                'helper_text' => 'MOQ means minimum amount of order quantity',
            ],
            'mmk_section' => [
                'description' => 'Myanmar Kyat Display Format',
                'buy_price' => 'Buy Price',
                'price' => 'Selling Price',
                'discount_price' => 'Discount Price',
            ],
            'attributes' => [
                'label' => 'Attributes',
                'attribute' => [
                    'label' => 'Attribute',
                    'placeholder' => 'Select',
                ],
                'value' => [
                    'label' => 'Value',
                    'placeholder' => 'Select',
                ],
            ],
            'images' => [
                'label' => 'Images',
                'helper_text' => 'For Clone Action, please add images at the end or clone in edit mode',
            ],
        ],
        'published' => [
            'label' => 'Published',
            'helper_text' => 'If you can publish when you want to show customers',
        ],
        'recommended' => [
            'label' => 'Recommend',
            'helper_text' => 'If you to recommend for your valuable customers',
        ],
        'shipping' => [
            'label' => 'Shipping',
            'description' => 'If you want specific shipping data you can change below options',
            'return' => [
                'label' => 'Return Product',
                'helper_text' => 'This product can be returned',
            ],
            'shipped' => [
                'label' => 'Shipping Product',
                'helper_text' => 'This product will be shipped',
            ],
        ],
        'single_product' => [
            'label' => 'Single Product Section',
            'description' => 'If your product has a single item you should use this field',
            'buy_price' => [
                'label' => 'Buy Price',
                'placeholder' => 'Original price',
                'helper_text' => 'Calculate for profit and loss',
            ],
            'price' => [
                'label' => 'Price',
                'placeholder' => 'Resell price',
            ],
            'quantity' => [
                'label' => 'Quantity',
                'helper_text' => 'Your in stock total amount',
            ],
            'moq' => [
                'label' => 'Min Amount Of Quantity (MOQ)',
                'helper_text' => 'Minimum amount of order quantity',
            ],
            'discount' => [
                'label' => 'Discount',
            ],
            'discount_price' => [
                'label' => 'Discount Price',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'category' => 'Category',
            'name' => 'Name',
            'supplier' => 'Supplier',
            'currency' => 'Currency',
            'is_public' => 'Published',
            'is_recommended' => 'Recommended',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
        'filters' => [
            'brand' => 'Brand Name',
            'category' => 'Category Name',
            'supplier' => 'Supplier Name',
        ],
    ],
];
