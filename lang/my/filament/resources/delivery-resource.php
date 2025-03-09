<?php

return [
    'model' => [
        'label' => 'ပို့ဆောင်ရေး',
        'plural_label' => 'ပို့ဆောင်ရေးများ',
    ],
    'navigation' => [
        'label' => 'ပို့ဆောင်ရေး',
        'group' => 'အော်ဒါများ',
    ],
    'table' => [
        'columns' => [
            'id' => 'အမှတ်စဉ်',
            'name' => 'အမည်',
            'phone' => 'ဖုန်းနံပါတ်',
            'address' => 'လိပ်စာ',
            'region' => 'တိုင်းဒေသကြီး',
            'township' => 'မြို့နယ်',
            'delivery_fee' => 'ပို့ဆောင်ခ',
            'cod' => 'လက်ခံရရှိမှ ငွေပေးချေမည်',
            'self_pickup' => 'ကိုယ်တိုင်လာယူမည်',
            'remark' => 'မှတ်ချက်',
            'created_at' => 'ပြုလုပ်ခဲ့သည့်အချိန်',
            'updated_at' => 'ပြင်ဆင်ခဲ့သည့်အချိန်',
        ],
        'filters' => [
            'search' => 'ရှာဖွေရန်',
        ],
    ],
    'form' => [
        'fields' => [
            'name' => 'အမည်',
            'phone' => 'ဖုန်းနံပါတ်',
            'address' => 'လိပ်စာ',
            'region' => 'တိုင်းဒေသကြီး',
            'township' => 'မြို့နယ်',
            'delivery_fee' => 'ပို့ဆောင်ခ',
            'cash_on_delivery' => 'လက်ခံရရှိမှ ငွေပေးချေမည်',
            'self_pickup' => 'ကိုယ်တိုင်လာယူမည်',
            'shipping_information' => 'ပို့ဆောင်ရေးအချက်အလက်များ',
            'remark' => 'မှတ်ချက်'
        ],
    ],
];