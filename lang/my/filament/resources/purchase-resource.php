<?php

return [
    'navigation' => [
        'label' => 'ဝယ်ယူမှုများ',
        'group' => 'စီးပွားရေး အပိုင်း',
        'plural_label' => 'ဝယ်ယူမှုများ',
        'single_label' => 'ဝယ်ယူမှု',
    ],
    'form' => [
        'products' => [
            'label' => 'ပစ္စည်းများ',
            'item' => [
                'product' => 'ပစ္စည်း',
                'variant' => 'မျိုးကွဲ',
                'stocks' => 'အရေအတွက်',
                'buy_price' => 'ဝယ်ဈေး',
                'sell_price' => 'ရောင်းဈေး',
                'total' => 'စုစုပေါင်း',
                'attributes' => [
                    'label' => 'အရည်အသွေးများ',
                    'attribute' => 'အရည်အသွေး',
                    'value' => 'တန်ဖိုး',
                    'select' => 'ရွေးချယ်ပါ',
                ],
            ],
        ],
        'supplier_id' => 'ပေးသွင်းသူ',
        'status' => 'အခြေအနေ',
        'total_amount' => 'စုစုပေါင်း ပမာဏ',
        'paid_amount' => 'ပေးချေပြီး ပမာဏ',
        'remaining_amount' => 'ကျန်ရှိသော ပမာဏ',
    ],
    'table' => [
        'columns' => [
            'seller' => 'ရောင်းချသူ',
            'supplier' => 'ပေးသွင်းသူ',
            'status' => 'အခြေအနေ',
            'total_amount' => 'စုစုပေါင်း ပမာဏ',
            'paid_amount' => 'ပေးချေပြီး ပမာဏ',
            'remaining_amount' => 'ကျန်ရှိသော ပမာဏ',
            'created_at' => 'ဖန်တီးခဲ့သည့်အချိန်',
            'updated_at' => 'ပြင်ဆင်ခဲ့သည့်အချိန်',
        ],
        'actions' => [
            'invoice' => 'ငွေတောင်းခံလွှာ',
            'import_stock' => 'ပစ္စည်းအရေအတွက်သွင်းရန်',
            'edit' => 'ပြင်ဆင်ရန်',
            'delete' => 'ဖျက်ရန်',
        ],
    ],
    'actions' => [
        'import_stock_success' => 'ပစ္စည်းအရေအတွက် အောင်မြင်စွာ သွင်းပြီးပါပြီ',
    ],
];
