<?php

return [
    'navigation' => [
        'label' => 'Gifts',
        'single' => 'Gift',
        'plural' => 'Gifts',
        'group' => 'Loyalty Program',
    ],

    'form' => [
        'sections' => [
            'gift_details' => 'Gift Details',
            'points_stock' => 'Points & Stock',
            'additional_info' => 'Additional Information',
        ],

        'name' => [
            'label' => 'Gift Name',
            'placeholder' => 'e.g., Coffee Cup, T-Shirt, Hat',
        ],

        'description' => [
            'label' => 'Description',
            'placeholder' => 'Describe the gift product...',
        ],

        'image' => [
            'label' => 'Gift Image',
        ],

        'points_required' => [
            'label' => 'Points Required',
            'helper_text' => 'How many points needed to exchange for this gift',
            'suffix' => 'points',
        ],

        'stock_quantity' => [
            'label' => 'Stock Quantity',
            'helper_text' => 'Available quantity',
            'suffix' => 'items',
        ],

        'is_active' => [
            'label' => 'Available for Exchange',
            'helper_text' => 'Enable/disable this gift for customer exchanges',
        ],

        'additional_data' => [
            'label' => 'Extra Details',
            'helper_text' => 'Add size, color, or other specifications',
        ],
    ],

    'table' => [
        'columns' => [
            'image' => 'Image',
            'name' => 'Gift Name',
            'points_required' => 'Points Required',
            'stock_quantity' => 'Stock Quantity',
            'is_active' => 'Active',
            'exchanges_count' => 'Exchanged',
            'created_at' => 'Created At',
        ],

        'filters' => [
            'status' => [
                'label' => 'Status',
                'placeholder' => 'All gifts',
                'active' => 'Active only',
                'inactive' => 'Inactive only',
            ],
            'low_stock' => [
                'label' => 'Low Stock (≤5 items)',
            ],
        ],
    ],

    'actions' => [
        'view' => 'View Gift',
        'edit' => 'Edit Gift',
        'delete' => 'Delete Gift',
    ],

    'messages' => [
        'low_stock_warning' => 'Low stock warning for this gift',
        'out_of_stock' => 'Out of stock',
    ],
];
