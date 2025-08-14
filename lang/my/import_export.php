<?php

return [
    'actions' => [
        'download_product_template' => 'ကုန်ပစ္စည်း Excel နမူနာ ဒေါင်းလုဒ်',
        'download_variant_template' => 'မျိုးကွဲ Excel နမူနာ ဒေါင်းလုဒ်',
        'export_products' => 'ကုန်ပစ္စည်းများ  ထုတ်ယူ',
        'export_variants' => 'မျိုးကွဲများ ထုတ်ယူ',
        'import_products' => 'ကုန်ပစ္စည်းများ တင်သွင်း',
        'import_variants' => 'မျိုးကွဲများ တင်သွင်း',
        'actions_group' => 'လုပ်ဆောင်ချက်များ',
    ],

    'form' => [
        'select_excel_file' => 'Excel ဖိုင် ရွေးချယ်ရန်',
        'download_template_first' => 'လိုအပ်သော ဖော်မတ်ကို ကြည့်ရှုရန် နမူနာကို အရင် ဒေါင်းလုဒ် လုပ်ပါ။',
        'ensure_products_exist' => 'လိုအပ်သော ဖော်မတ်ကို ကြည့်ရှုရန် နမူနာကို အရင် ဒေါင်းလုဒ် လုပ်ပါ။ မျိုးကွဲများ တင်သွင်းမီ ကုန်ပစ္စည်းများ ရှိနေကြောင်း သေချာပါစေ။',
    ],

    'notifications' => [
        'products_imported_success' => 'Excel ကုန်ပစ္စည်းများ အောင်မြင်စွာ တင်သွင်းပြီးပါပြီ!',
        'variants_imported_success' => 'Excel မျိုးကွဲများ အောင်မြင်စွာ တင်သွင်းပြီးပါပြီ!',
        'error_importing_products' => 'Excel ကုန်ပစ္စည်းများ တင်သွင်းရာတွင် အမှားရှိသည်',
        'error_importing_variants' => 'Excel မျိုးကွဲများ တင်သွင်းရာတွင် အမှားရှိသည်',
    ],

    'file_names' => [
        'product_template' => 'product_import_template.xlsx',
        'variant_template' => 'variant_import_template.xlsx',
        'products_export' => 'products_:timestamp.xlsx',
        'variants_export' => 'variants_:timestamp.xlsx',
    ],
];
