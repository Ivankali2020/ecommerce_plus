<?php

return [
    'navigation' => [
        'label' => 'အော်ဒါပစ္စည်းများ',
        'group' => 'အော်ဒါအပိုင်း',
        'plural' => 'အော်ဒါပစ္စည်းများ',
        'single' => 'အော်ဒါပစ္စည်း',
    ],
    'columns' => [
        'order' => [
            'invoice_number' => 'ငွေတောင်းခံလွှာနံပါတ်',
        ],
        'product' => [
            'name' => 'ပစ္စည်းအမည်',
            'category' => 'အမျိုးအစား',
        ],
        'attributes' => 'ယူနစ်',
        'original_buy_price' => '၀ယ်ရင်းစျေး',
        'original_price' => 'မူလစျေး',
        'selling_price' => 'ရောင်းစျေး',
        'discount_price' => 'လျှော့စျေး',
        'discount' => 'လျှော့စျေး %',
        'quantity' => 'အရေအတွက်',
        'profit_loss' => 'အမြတ်/အရှုံး',
        'sub_price' => 'စုစုပေါင်း',
        'total_amount' => 'စုစုပေါင်းပမာဏ',
        'created_at' => 'ဖန်တီးသည့်ရက်စွဲ',
    ],
    'actions' => [
        'edit' => 'ပြင်ဆင်ရန်',
        'delete' => 'ဖျက်ရန်',
        'restore' => 'ပြန်လည်ရယူရန်',
    ],
    'filters' => [
        'date_range' => 'ရက်စွဲအပိုင်းအခြား',
        'created_from' => 'စတင်ရက်',
        'created_until' => 'ပြီးဆုံးရက်',
        'product' => 'ပစ္စည်း',
        'order' => 'အော်ဒါ နံပါတ်',
    ],
];
