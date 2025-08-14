<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {

        // Basic Plan - 1 months
        SubscriptionPlan::create([
            'name' => 'Free Plan',
            'description' => 'Perfect for small businesses just getting started with e-commerce.',
            'price' => 0,
            'duration_months' => 1,
            'features' => 'ရိုးရှင်းသော စီမံခန့်ခွဲမူ, အကန့်အသတ် မရှိသော ထုတ်ကုန်များ, အကန့်အသတ် မရှိသော ဝန်ထမ်းအကောင့်များ, ပစွည်း အသွင်း / အထုတ်  ကို Excel ဖိုင်နှင့် တင်သွင်းခြင်း, POS စနစ်, ၀ယ်ယူသူ စီမံခန့်ခွဲမှု, ပစ္စည်းပေးသွင်းသူ စီမံခန့်ခွဲမှု, ဖောက်သည် စီမံခန့်ခွဲမှု, ဝယ်ယူမှုအော်ဒါ စီမံခန့်ခွဲမှု, ရောင်းချမှုအော်ဒါ စီမံခန့်ခွဲမှု, အသုံးစရိတ် စီမံခန့်ခွဲမှု, ပစ္စည်းပေးပို့မှု စီမံခန့်ခွဲမှု, ငွေလဲနှုန်း စီမံခန့်ခွဲမှု, ကိုယ်ပိုင် ဒိုမိန်း ခွဲ တခု, ကိုယ်ပိုင် Dashboard, ကိုယ်ပိုင် Website သီးသန့်, CSR (Customer Service Representative) အကူအညီ ( comming soon )',
            'is_active' => false,
        ]);

        // Basic Plan - 1 months
        SubscriptionPlan::create([
            'name' => '၁ လ အသုံးပြုခွင့်',
            'description' => 'စတင်အသုံးပြုလိုသူများအတွက် အထူးသင့်လျော်သည်။',
            'price' => 14999,
            'duration_months' => 1,
            'features' => 'ရိုးရှင်းသော စီမံခန့်ခွဲမူ, အကန့်အသတ် မရှိသော ထုတ်ကုန်များ, အကန့်အသတ် မရှိသော ဝန်ထမ်းအကောင့်များ, ပစွည်း အသွင်း / အထုတ် ကို Excel ဖိုင်, POS စနစ်, ၀ယ်ယူသူ စီမံခန့်ခွဲမှု, ပစ္စည်းပေးသွင်းသူ စီမံခန့်ခွဲမှု, ဖောက်သည် စီမံခန့်ခွဲမှု, ဝယ်ယူမှုအော်ဒါ စီမံခန့်ခွဲမှု, ရောင်းချမှုအော်ဒါ စီမံခန့်ခွဲမှု, အသုံးစရိတ် စီမံခန့်ခွဲမှု, ပစ္စည်းပေးပို့မှု စီမံခန့်ခွဲမှု, ငွေလဲနှုန်း စီမံခန့်ခွဲမှု, ကိုယ်ပိုင် ဒိုမိန်း ခွဲ တခု, ကိုယ်ပိုင် Dashboard, ကိုယ်ပိုင် Website သီးသန့်, Gift Points System, CSR (Customer Service Representative) အကူအညီ ( comming soon )',
            'is_active' => true,
        ]);

        // Standard Plan - 6 months
        SubscriptionPlan::create([
            'name' => '6 လ အသုံးပြုခွင့်',
            'description' => 'အလယ်အလတ် စီးပွားရေးလုပ်ငန်းများအတွက် အထူးသင့်လျော်သည်။',
            'price' => 89999,
            'duration_months' => 6,
            'features' => 'ရိုးရှင်းသော စီမံခန့်ခွဲမူ, အကန့်အသတ် မရှိသော ထုတ်ကုန်များ, အကန့်အသတ် မရှိသော ဝန်ထမ်းအကောင့်များ, ပစွည်း အသွင်း / အထုတ် ကို Excel ဖိုင်, POS စနစ်, ၀ယ်ယူသူ စီမံခန့်ခွဲမှု, ပစ္စည်းပေးသွင်းသူ စီမံခန့်ခွဲမှု, ဖောက်သည် စီမံခန့်ခွဲမှု, ဝယ်ယူမှုအော်ဒါ စီမံခန့်ခွဲမှု, ရောင်းချမှုအော်ဒါ စီမံခန့်ခွဲမှု, အသုံးစရိတ် စီမံခန့်ခွဲမှု, ပစ္စည်းပေးပို့မှု စီမံခန့်ခွဲမှု, အခြေခံ အစီရင်ခံစာများ, ကိုယ်ပိုင် ဒိုမိန်း ခွဲ တခု, ကိုယ်ပိုင် Dashboard, ကိုယ်ပိုင် Website သီးသန့်, ကိုယ်ပိုင် API ( comming soon ), Gift Points System, CSR (Customer Service Representative) အကူအညီ ( comming soon )',
            'is_active' => true,
        ]);

        // Premium Plan - 12 months
        SubscriptionPlan::create([
            'name' => '12 လ အသုံးပြုခွင့်',
            'description' => 'စီးပွားရေးလုပ်ငန်းများအတွက် အထူးသင့်လျော်သည်။',
            'price' => 179999,
            'duration_months' => 12,
            'features' => 'ရိုးရှင်းသော စီမံခန့်ခွဲမူ, အကန့်အသတ် မရှိသော ထုတ်ကုန်များ, အကန့်အသတ် မရှိသော ဝန်ထမ်းအကောင့်များ, ပစွည်း အသွင်း / အထုတ် ကို Excel ဖိုင်, POS စနစ်, ၀ယ်ယူသူ စီမံခန့်ခွဲမှု, ပစ္စည်းပေးသွင်းသူ စီမံခန့်ခွဲမှု, ဖောက်သည် စီမံခန့်ခွဲမှု, ဝယ်ယူမှုအော်ဒါ စီမံခန့်ခွဲမှု, ရောင်းချမှုအော်ဒါ စီမံခန့်ခွဲမှု, အသုံးစရိတ် စီမံခန့်ခွဲမှု, ပစ္စည်းပေးပို့မှု စီမံခန့်ခွဲမှု, အခြေခံ အစီရင်ခံစာများ, ကိုယ်ပိုင် ဒိုမိန်း ခွဲ တခု, ကိုယ်ပိုင် Dashboard, ကိုယ်ပိုင် Website သီးသန့်, Gift Points System, ကိုယ်ပိုင် APP ( comming soon ), ကိုယ်ပိုင် API ( comming soon ), CSR (Customer Service Representative) အကူအညီ ( comming soon ), ၂၄/၇ အခမဲ့ တိုင်ပင်ဆွေးနွေးခြင်း',
            'is_active' => true,
        ]);
    }
}
