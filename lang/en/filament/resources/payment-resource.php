<?php

return [
    'navigation' => [
        'label' => 'Payments',
        'group' => 'Payment Section',
        'plural' => 'Payments',
        'single' => 'Payment',
    ],

    'table' => [
        'columns' => [
            'name' => 'Name',
            'accounts' => 'Accounts',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
        'filters' => [
            'created_at' => 'Created At',
        ],
        'actions' => [
            'create' => 'Create Payment',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ],
    ],

    'form' => [
        'name' => [
            'label' => 'Name',
            'helper_text' => 'K PAY, Wave Pay, CB Bank...',
        ],
        'accounts' => [
            'label' => 'Accounts',
            'name' => [
                'label' => 'Name',
                'placeholder' => 'U Ivan Phyo',
            ],
            'number' => [
                'label' => 'Number',
                'placeholder' => '09 453183072',
            ],
        ],
        'seller' => [
            'label' => 'Seller',
        ],
        'currency' => [
            'label' => 'Currency',
            'helper_text' => 'We decided to use only mmk currency for phase one',
        ],
        'logo' => [
            'label' => 'Logo',
        ],
    ],

    'messages' => [
        'created' => 'Payment created successfully',
        'updated' => 'Payment updated successfully',
        'deleted' => 'Payment deleted successfully',
    ],
];
