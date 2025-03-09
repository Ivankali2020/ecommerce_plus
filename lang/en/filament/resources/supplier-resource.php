<?php

return [
    'navigation' => [
        'label' => 'Suppliers',
        'group' => 'Management',
    ],
    'table' => [
        'columns' => [
            'seller.name' => 'Seller',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'township.mm_name' => 'Township',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
    ],
    'form' => [
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'about' => 'About',
        'township_id' => 'Township',
    ],
];