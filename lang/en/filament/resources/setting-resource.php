<?php

return [
    'navigation' => [
        'label' => 'Settings',
        'single' => 'Setting',
        'plural' => 'Settings',
    ],

    'form' => [
        'tabs' => [
            'general' => 'General Settings',
            'social_media' => 'Social Media',
            'content_pages' => 'Content Pages',
            'faq' => 'FAQ',
        ],

        'general' => [
            'point_percentage' => [
                'label' => 'Point Percentage',
                'helper_text' => 'Percentage of order value to be awarded as points',
            ],
            'tax_percentage' => [
                'label' => 'Tax Percentage',
                'helper_text' => 'Tax percentage to be applied on orders',
            ],
            'referral_points' => [
                'label' => 'Referral Points',
                'helper_text' => 'Points awarded for successful referrals',
            ],
        ],

        'social_media' => [
            'facebook' => [
                'label' => 'Facebook URL',
                'placeholder' => 'https://facebook.com/yourpage',
            ],
            'twitter' => [
                'label' => 'Twitter URL',
                'placeholder' => 'https://twitter.com/yourpage',
            ],
            'youtube' => [
                'label' => 'YouTube URL',
                'placeholder' => 'https://youtube.com/yourpage',
            ],
            'instagram' => [
                'label' => 'Instagram URL',
                'placeholder' => 'https://instagram.com/yourpage',
            ],
        ],

        'content_pages' => [
            'privacy_policy' => [
                'label' => 'Privacy Policy',
                'helper_text' => 'Privacy policy content for your store',
            ],
            'terms_conditions' => [
                'label' => 'Terms & Conditions',
                'helper_text' => 'Terms and conditions for your store',
            ],
            'about_us' => [
                'label' => 'About Us',
                'helper_text' => 'Information about your business',
            ],
            'returns_exchanges' => [
                'label' => 'Returns & Exchanges Policy',
                'helper_text' => 'Return and exchange policy for your store',
            ],
        ],

        'faq' => [
            'label' => 'Frequently Asked Questions',
            'question' => [
                'label' => 'Question',
                'placeholder' => 'Enter frequently asked question',
            ],
            'answer' => [
                'label' => 'Answer',
                'placeholder' => 'Enter detailed answer',
            ],
            'add_item' => 'Add FAQ Item',
        ],
    ],

    'table' => [
        'columns' => [
            'point_percentage' => 'Point Percentage',
            'tax_percentage' => 'Tax Percentage',
            'referral_points' => 'Referral Points',
        ],
    ],
];
