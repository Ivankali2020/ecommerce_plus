<?php

return [
    'navigation' => [
        'label' => 'အသုံးပြုသူများ',
        'group' => 'အသုံးပြုသူ စီမံခန့်ခွဲမှု',
        'plural' => 'အသုံးပြုသူများ',
        'singular' => 'အသုံးပြုသူ',
    ],
    'table' => [
        'columns' => [
            'name' => 'အမည်',
            'email' => 'အီးမေးလ်',
            'phone' => 'ဖုန်းနံပါတ်',
            'created_at' => 'ဖန်တီးခဲ့သည့်အချိန်',
        ],
        'filters' => [
            'verified' => 'အတည်ပြုပြီး',
            'unverified' => 'အတည်မပြုရသေး',
        ],
    ],
    'form' => [
        'name' => [
            'label' => 'အမည်',
            'placeholder' => 'အမည်ထည့်ပါ',
        ],
        'email' => [
            'label' => 'အီးမေးလ်',
            'placeholder' => 'အီးမေးလ်ထည့်ပါ',
        ],
        'phone' => [
            'label' => 'ဖုန်းနံပါတ်',
            'placeholder' => 'ဖုန်းနံပါတ်ထည့်ပါ',
        ],
        'password' => [
            'label' => 'စကားဝှက်',
            'placeholder' => 'စကားဝှက်ထည့်ပါ',
        ],
        'password_confirmation' => [
            'label' => 'စကားဝှက်အတည်ပြုခြင်း',
            'placeholder' => 'စကားဝှက်ကို ထပ်မံထည့်သွင်းပါ',
        ],
    ],
];