<?php

return [
    'model' => [
        'label' => 'အမှတ်တံဆိပ်',
        'plural_label' => 'အမှတ်တံဆိပ်များ',
    ],
    'navigation' => [
        'label' => 'အမှတ်တံဆိပ်',
        'group' => 'စီးပွားရေးဆိုင်ရာ',
    ],
    'table' => [
        'columns' => [
            'seller' => 'ရောင်းချသူ',
            'name' => 'အမည်',
            'description' => 'ဖော်ပြချက်',
            'deleted_at' => 'ဖျက်ထားသည့်အချိန်',
            'created_at' => 'ဖန်တီးခဲ့သည့်အချိန်',
            'updated_at' => 'ပြင်ဆင်ခဲ့သည့်အချိန်',
        ],
    ],
    'form' => [
        'seller' => 'ရောင်းချသူ',
        'name' => 'အမည်',
        'name_helper' => 'အမှတ်တံဆိပ်အမည်ထည့်ပါ',
        'logo' => 'လိုဂို',
        'description' => 'ဖော်ပြချက်',
    ],
];
