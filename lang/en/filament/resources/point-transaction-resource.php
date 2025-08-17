<?php

return [
    'navigation' => [
        'label' => 'Point Transactions',
        'single' => 'Point Transaction',
        'plural' => 'Point Transactions',
        'group' => 'Loyalty Program',
    ],

    'form' => [
        'user_id' => [
            'label' => 'User',
        ],

        'type' => [
            'label' => 'Transaction Type',
            'options' => [
                'earned' => 'Earned',
                'spent' => 'Spent',
                'expired' => 'Expired',
                'refunded' => 'Refunded',
            ],
        ],

        'points' => [
            'label' => 'Points',
        ],

        'balance_after' => [
            'label' => 'Balance After',
        ],

        'reference_type' => [
            'label' => 'Reference Type',
        ],

        'reference_id' => [
            'label' => 'Reference ID',
        ],

        'description' => [
            'label' => 'Description',
        ],

        'metadata' => [
            'label' => 'Metadata',
        ],

        'expires_at' => [
            'label' => 'Expires At',
        ],
    ],

    'table' => [
        'columns' => [
            'user' => 'User',
            'transaction_type' => 'Transaction Type',
            'points' => 'Points',
            'balance_after' => 'Balance After',
            'type' => 'Type',
            'reference' => 'Reference',
            'ref_id' => 'Ref ID',
            'description' => 'Description',
            'expires_at' => 'Expires At',
            'created' => 'Created',
            'updated' => 'Updated',
        ],

        'filters' => [
            'type' => [
                'label' => 'Transaction Type',
            ],
            'has_expiry' => [
                'label' => 'Has Expiry Date',
            ],
            'expired' => [
                'label' => 'Expired',
            ],
            'created_at' => [
                'label' => 'Date Range',
                'created_from' => 'Created From',
                'created_until' => 'Created Until',
            ],
            'date_range' => [
                'label' => 'Date Range',
                'from' => 'From',
                'until' => 'Until',
            ],
        ],

        'values' => [
            'never' => 'Never',
            'na' => 'N/A',
        ],
    ],

    'actions' => [
        'view' => 'View Transaction',
        'edit' => 'Edit Transaction',
        'delete' => 'Delete Transaction',
    ],

    'empty_state' => [
        'heading' => 'No Point Transactions Found',
        'description' => 'Start by creating your first point transaction.',
    ],

    'statuses' => [
        'earned' => 'Earned',
        'spent' => 'Spent',
        'expired' => 'Expired',
        'refunded' => 'Refunded',
    ],
];
