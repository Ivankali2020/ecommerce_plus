<?php

return [
    'navigation' => [
        'label' => 'Expanse',
        'group' => 'Finance',
    ],
    'form' => [
        'expanse_category_id' => [
            'label' => 'Category',
        ],
        'amount' => [
            'label' => 'Amount',
        ],
        'payment_method' => [
            'label' => 'Payment Method',
        ],
        'description' => [
            'label' => 'Description',
        ],
    ],
    'table' => [
        'columns' => [
            'category_name' => 'Category',
            'payment_method' => 'Payment Method',
            'description' => 'Description',
            'amount' => 'Amount',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
    ],
];