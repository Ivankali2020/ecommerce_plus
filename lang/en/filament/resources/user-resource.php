<?php

return [
    'navigation' => [
        'label' => 'Users',
        'group' => 'User Management',
        'plural' => 'Users',
        'singular' => 'User',
    ],
    'table' => [
        'columns' => [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'created_at' => 'Created At',
        ],
        'filters' => [
            'verified' => 'Verified',
            'unverified' => 'Unverified',
        ],
    ],
    'form' => [
        'name' => [
            'label' => 'Name',
            'placeholder' => 'Enter name',
        ],
        'email' => [
            'label' => 'Email',
            'placeholder' => 'Enter email',
        ],
        'phone' => [
            'label' => 'Phone',
            'placeholder' => 'Enter phone number',
        ],
        'password' => [
            'label' => 'Password',
            'placeholder' => 'Enter password',
        ],
        'password_confirmation' => [
            'label' => 'Confirm Password',
            'placeholder' => 'Confirm your password',
        ],
    ],
];
