<?php

return [
    'label' => 'Referral History',
    'plural' => 'Referral Histories',

    'navigation' => [
        'group' => 'Users',
    ],

    'columns' => [
        'referrer' => 'Referrer',
        'referred' => 'Referred User',
        'points' => 'Points Awarded',
        'verified_at' => 'Verified At',
        'created_at' => 'Created At',
    ],

    'filters' => [
        'verified' => [
            'label' => 'Verification Status',
            'placeholder' => 'All',
            'true' => 'Verified only',
            'false' => 'Pending only',
        ],
    ],
];
