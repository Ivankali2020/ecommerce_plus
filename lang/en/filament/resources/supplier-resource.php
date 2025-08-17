<?php

return [
    'navigation' => [
        'label' => 'Suppliers',
        'group' => 'Management',
    ],
    'table' => [
        'columns' => [
            'seller' => 'Seller',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'township' => 'Township',
            'region' => 'Region',
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
        'region_id' => 'Region',
    ],
];
