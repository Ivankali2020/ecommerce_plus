<?php

return [
    'navigation' => [
        'label' => 'ပစ္စည်းသွင်းသူများ',
        'group' => 'စီမံခန့်ခွဲမှု',
    ],
    'table' => [
        'columns' => [
            'seller.name' => 'ရောင်းချသူ',
            'name' => 'အမည်',
            'email' => 'အီးမေးလ်',
            'phone' => 'ဖုန်းနံပါတ်',
            'township.mm_name' => 'မြို့နယ်',
            'created_at' => 'ဖန်တီးခဲ့သည့်အချိန်',
            'updated_at' => 'ပြင်ဆင်ခဲ့သည့်အချိန်',
        ],
    ],
    'form' => [
        'name' => 'အမည်',
        'email' => 'အီးမေးလ်',
        'phone' => 'ဖုန်းနံပါတ်',
        'about' => 'အကြောင်းအရာ',
        'township_id' => 'မြို့နယ်',
    ],
];
