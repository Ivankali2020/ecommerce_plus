<?php

return [
    'model' => [
        'label' => 'Delivery',
        'plural_label' => 'Deliveries',
    ],
    'navigation' => [
        'label' => 'Delivery',
        'group' => 'Order Section',
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'address' => 'Address',
            'region' => 'Region',
            'township' => 'Township',
            'delivery_fee' => 'Delivery Fee',
            'cod' => 'Cash on Delivery',
            'self_pickup' => 'Self Pickup',
            'remark' => 'Remark',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
        'filters' => [
            'search' => 'Search',
        ],
    ],
    'form' => [
        'fields' => [
            'name' => 'Name',
            'phone' => 'Phone',
            'address' => 'Address',
            'region' => 'Region',
            'township' => 'Township',
            'delivery_fee' => 'Delivery Fee',
            'cash_on_delivery' => 'Cash on Delivery',
            'self_pickup' => 'Self Pickup',
            'shipping_information' => 'Shipping Information',
            'remark' => 'Remark',
        ],
    ],
];
