<?php

return [
    'navigation' => [
        'label' => 'Gift Exchanges',
        'single' => 'Gift Exchange',
        'plural' => 'Gift Exchanges',
        'group' => 'Loyalty Program',
    ],

    'form' => [
        'sections' => [
            'exchange_details' => 'Exchange Details',
            'status_tracking' => 'Status & Tracking',
            'delivery_info' => 'Delivery Information',
            'system_info' => 'System Information',
        ],

        'fields' => [
            'user' => 'Customer',
            'gift' => 'Gift',
            'points_spent' => 'Points Spent',
            'points_suffix' => 'points',
            'status' => [
                'label' => 'Status',
                'options' => [
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ],
            ],
            'processed_at' => 'Processed At',
            'shipped_at' => 'Shipped At',
            'delivered_at' => 'Delivered At',
            'delivery_address' => 'Delivery Address',
            'notes' => 'Exchange Notes',
            'seller' => 'Seller',
            'tenant' => 'Tenant',
            'point_transaction' => 'Point Transaction',
            'gift_snapshot' => 'Gift Snapshot (JSON)',
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'customer' => 'Customer',
            'gift' => 'Gift',
            'points' => 'Points',
            'status' => 'Status',
            'ordered' => 'Ordered',
            'processed' => 'Processed',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'last_updated' => 'Last Updated',
        ],

        'filters' => [
            'status' => [
                'label' => 'Status',
            ],
            'created_at' => [
                'label' => 'Date Range',
                'from' => 'From',
                'to' => 'To',
            ],
            'gift' => [
                'label' => 'Gift',
            ],
            'date_range' => [
                'label' => 'Date Range',
                'from' => 'From',
                'until' => 'Until',
            ],
        ],
    ],

    'actions' => [
        'view' => 'View Exchange',
        'edit' => 'Edit Exchange',
        'mark_processed' => [
            'label' => 'Mark as Processed',
            'confirmation' => 'Are you sure you want to mark this exchange as processed?',
        ],
        'mark_shipped' => [
            'label' => 'Mark as Shipped',
            'confirmation' => 'Are you sure you want to mark this exchange as shipped?',
        ],
        'mark_delivered' => [
            'label' => 'Mark as Delivered',
            'confirmation' => 'Are you sure you want to mark this exchange as delivered?',
        ],
        'export' => [
            'label' => 'Export Exchanges',
        ],
    ],

    'badges' => [
        'pending_count' => 'Pending exchanges',
    ],

    'empty_state' => [
        'heading' => 'No Gift Exchanges Found',
        'description' => 'Start by creating your first gift exchange.',
        'create_first' => 'Create First Exchange',
    ],
];
