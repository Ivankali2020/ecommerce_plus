<?php

return [
    'model' => [
        'label' => 'Brand',
        'plural_label' => 'Brands',
    ],
    'navigation' => [
        'label' => 'Brand',
        'group' => 'Business Section',
    ],
    'table' => [
        'columns' => [
            'seller' => 'Seller',
            'name' => 'Name',
            'description' => 'Description',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
    ],
    'form' => [
        'seller' => 'Seller',
        'name' => 'Name',
        'name_helper' => 'Enter the brand name',
        'logo' => 'Logo',
        'description' => 'Description',
    ],
];
