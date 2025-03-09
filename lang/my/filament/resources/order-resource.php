<?php

return [
    'navigation' => [
        'label' => 'အော်ဒါများ',
        'group' => 'စီးပွားရေး အပိုင်း',
        'plural' => 'အော်ဒါများ',
        'single' => 'အော်ဒါ',
    ],
    'form' => [
        'products' => [
            'label' => 'ပစ္စည်းများ',
            'helper_text' => '* မြန်မာကျပ်ဖြင့် တွက်ချက်ထားသည် *',
            'details' => [
                'label' => 'အသေးစိတ် အချက်အလက်များ',
                'buy_price' => '၀ယ်ရင်းစျေး',
                'old_price' => 'မူလစျေး',
                'old_discount_price' => 'မူလလျှော့စျေး',
                'exchange_rate' => 'ငွေလဲနှုန်း',
                'currency' => 'ငွေကြေး',
            ],
            'selection' => [
                'product' => 'ပစ္စည်း',
                'quantity' => 'အရေအတွက်',
                'quantity_error' => ' သာကျန်ရှိပါတော့သည်',
                'variant' => 'မျိုးကွဲ',
                'price' => 'စျေးနှုန်း',
                'discount' => 'လျှော့စျေး',
                'discount_price' => 'လျှော့စျေးဖြင့် ရောင်းစျေး',
                'sub_price' => 'စုစုပေါင်းစျေး',
            ],
        ],
        'attributes' => [
            'label' => 'အရည်အသွေးများ',
            'attribute' => 'အရည်အသွေး',
            'value' => 'တန်ဖိုး',
        ],
        'order_info' => [
            'label' => 'အော်ဒါ အချက်အလက်',
            'invoice_number' => 'ငွေတောင်းခံလွှာနံပါတ်',
            'seller' => 'ရောင်းချသူ',
            'walk_in' => [
                'label' => 'လာရောက်၀ယ်ယူ',
                'helper_text' => 'ဖောက်သည် တိုက်ရိုက်လာရောက်၀ယ်ယူ',
            ],
            'customer' => [
                'name' => 'ဖောက်သည်အမည်',
                'email' => 'အီးမေးလ်',
                'phone' => 'ဖုန်းနံပါတ်',
                'address' => 'လိပ်စာ',
                'region' => 'တိုင်းဒေသကြီး',
                'township' => 'မြို့နယ်',
            ],
            'payment' => 'ငွေပေးချေမှု',
            'status' => [
                'label' => 'အခြေအနေ',
                'options' => [
                    'new' => 'အသစ်',
                    'processing' => 'ဆောင်ရွက်နေဆဲ',
                ],
            ],
            'purchase_date' => '၀ယ်ယူသည့်ရက်စွဲ',
        ],
        'price_info' => [
            'label' => 'စျေးနှုန်း အချက်အလက်',
            'sub_total' => [
                'label' => 'ကုန်ပစ္စည်းတန်ဖိုး',
                'helper_text' => 'ပစ္စည်းစျေးနှုန်း စုစုပေါင်း',
            ],
            'delivery_fees' => 'ပို့ဆောင်ခ',
            'tax' => [
                'label' => 'အခွန်',
                'helper_text' => 'စုစုပေါင်းပေါ် အခြေခံ၍',
                'amount' => 'အခွန်ပမာဏ',
            ],
            'total_price' => [
                'label' => 'စုစုပေါင်း',
                'helper_text' => 'အားလုံးပေါင်း စုစုပေါင်း',
            ],
        ],
        'payment_slip' => 'ငွေပေးချေမှု ပြေစာ',
    ],
    'table' => [
        'columns' => [
            'user' => 'ဖောက်သည်',
            'payment' => 'ငွေပေးချေမှု',
            'address' => 'လိပ်စာ',
            'status' => 'အခြေအနေ',
            'tax_price' => 'အခွန်ငွေ',
            'sub_total' => 'ကုန်ပစ္စည်းတန်ဖိုး',
            'total_price' => 'စုစုပေါင်း',
            'created_at' => 'ဖန်တီးခဲ့သည့်အချိန်',
            'updated_at' => 'ပြင်ဆင်ခဲ့သည့်အချိန်',
        ],
        'filters' => [
            'trashed' => 'ပယ်ဖျက်ထားသော',
        ],
        'actions' => [
            'invoice' => 'ငွေတောင်းခံလွှာ',
            'edit' => 'ပြင်ဆင်ရန်',
            'delete' => 'ဖျက်ရန်',
            'restore' => 'ပြန်လည်ရယူရန်',
        ],
        'summary' => [
            'total_amount' => 'စုစုပေါင်း ပမာဏ',
        ],
    ],
];
