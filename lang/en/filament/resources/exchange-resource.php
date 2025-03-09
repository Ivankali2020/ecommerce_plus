<?php

return [
    'navigation' => [
        'group' => 'Money Section',
        'label' => 'Exchanges',
    ],
    'model' => [
        'label' => 'Exchange',
    ],
    'form' => [
        'seller_id' => [
            'label' => 'Seller',
        ],
        'currency_id' => [
            'label' => 'Currency',
        ],
        'rate' => [
            'label' => 'Exchange Rate',
            'helper' => 'Calculate based on 1 USD',
        ],
    ],
    'table' => [
        'seller_name' => 'Seller',
        'currency_code' => 'Currency',
        'rate' => 'Rate',
        'rate_description' => 'Calculated based on 1 USD',
    ],
];