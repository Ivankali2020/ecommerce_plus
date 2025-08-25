<?php

namespace Database\Seeders;

use App\Helper\AttributeType;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Delivery;
use App\Models\Exchange;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Region;
use App\Models\Seller;
use App\Models\Tenant;
use App\Models\Township;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Domain;

class MultiStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $this->call([
                RegionSeeder::class,
                // CurrencySeeder::class,
            ]);

            // Deliveries

            // create tenant
            $tenant = Tenant::create([
                'data' => [
                    'domain' => 'store',
                ],
            ]);

            $user = Seller::factory(1)
                ->state([
                    'email' => 'store@gmail.com',
                    'name' => 'MULTI STORE',
                    'phone' => '09 454949610',
                    'description' => 'ကုန်မျိုးဆုံဆိုင်',
                    'email_verified_at' => now(),
                    'tenant_id' => $tenant->id,
                ])
                // ->has(Attribute::factory(3)->has(AttributeValue::factory(3), 'values'))
                ->has(Payment::factory(5)->state(fn () => ['tenant_id' => isset($tenant) ? $tenant->id : null])->has(PaymentAccount::factory(2), 'accounts'))
                ->has(User::factory(3)->state(fn () => ['tenant_id' => isset($tenant) ? $tenant->id : null])->has(UserAddress::factory(2), 'addresses'))
                ->create()->toArray()[0];

            // create domains
            $domain = Domain::create([
                'domain' => 'store'.'.'.config('tenancy.central_domains')[0],
                'tenant_id' => $tenant->id,
            ]);

            // attach tenant to seller
            DB::table('tenant_seller')->insert([
                'tenant_id' => $tenant->id,
                'seller_id' => $user['id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create delivery data for the seller
            $this->createDeliveries($user['id'], $tenant->id);

            // add attributes sample for store
            $attributes = [
                ['name' => 'အရောင်', 'type' => AttributeType::Color],
                ['name' => 'အရွယ်အစား', 'type' => AttributeType::Text],
                ['name' => 'အထည်ပုံစံ', 'type' => AttributeType::Text],
            ];
            foreach ($attributes as $attr) {
                $attribute = new Attribute;
                $attribute->name = $attr['name'];
                $attribute->type = $attr['type'];
                $attribute->seller_id = $user['id'];
                $attribute->tenant_id = $tenant->id;
                $attribute->save();

                // Create sample attribute values
                for ($i = 0; $i < 3; $i++) {
                    AttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'value' => fake()->word(),
                        'color_code' => fake()->hexColor(),
                    ]);
                }
            }

            $categories = [
                'စည်သွပ်ဗူး',
                'လက်ဖက်နှင့်အကြော်စုံ',
                'ငါးပိငါးခြောက်',
                'အသင့်စား',
                'မီးဖိုချောင်သုံးပစ္စည်းများ',
                'ဆေးဝါးနှင့်အလှကုန်',
                'မုန့်ပဲသရေစာ',
                'အခြား',
                'အမှုန့်မျိုးစုံ',
                'သောက်စရာ',
                'အစေ့အဆံ',
                'အသုံးအဆောင်',
                'ပဲအမျိုးမျိုး',
                'Coffee & tea',
                'ရွှေတောမှထုတ်ကုန်များ',
            ];

            foreach ($categories as $cat) {
                $category = new Category;
                $category->seller_id = $user['id'];
                $category->tenant_id = $tenant->id;
                $category->name = $cat;
                $category->save();
            }

            $brands = [
                'Best',
                'Ready',
                'အိမ်ချက်',
                'ထိုင်း',
                'လက်ဖက်နှင့်အကြော်စုံ',
                'ငါးပိငါးခြောက်',
                'Noodle',
                'မီးဖိုချောင်သုံးပစ္စည်းနဲ့ဟင်းခက်အမွှေးအကြိုင်',
                'အလှကုန်ဆေးများ',
                'အခြား',
            ];

            foreach ($brands as $cat) {
                $brand = new Brand;
                $brand->name = $cat;
                $brand->seller_id = $user['id'];
                $brand->tenant_id = $tenant->id;
                $brand->save();
                //                $brand->addMediaFromUrl(fake()->imageUrl(width: 200,height: 200,randomize: false,word: 'Brand',gray: true))
                //                ->toMediaCollection('logos');
            }

            $products = [
                ['name' => 'ဒညင်းငပိ', 'price' => 480, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A001'],
                ['name' => 'ရေဦးပဲငပိ', 'price' => 450, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A002'],
                ['name' => 'မန်းကျဥ်းသီးစိမ်းငပိထောင်း', 'price' => 400, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A003'],
                ['name' => 'ကင်ပွန်းချဥ်ရွက်ကြော်', 'price' => 400, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A004'],
                ['name' => 'ချဥ်ပေါင်ကြော်', 'price' => 400, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A005'],
                ['name' => 'ငှက်ပျော်အူ', 'price' => 650, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A006'],
                ['name' => 'ငါးသလောက်ဥကြော်', 'price' => 1050, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A007'],
                ['name' => 'မရမ်းသီးငပိချက်', 'price' => 480, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A008'],
                ['name' => 'ငါးပိချက်', 'price' => 490, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A009'],
                ['name' => 'ရှယ်ပုစွန်ခြောက်ဘာလချောင်ကြော်', 'price' => 490, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A010'],
                ['name' => 'ဂွေး‌တောက်ရွက်', 'price' => 600, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A011'],
                ['name' => 'ငါးသလောက်ပေါင်း', 'price' => 530, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A012'],
                ['name' => 'ဒညင်းသီးပြုတ်', 'price' => 400, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A013'],
                ['name' => 'ငါးခူမျှစ်ချဥ်', 'price' => 750, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A014'],
                ['name' => 'မုန်ဟင်းခါးအနှစ်', 'price' => 1000, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A015'],
                ['name' => 'ကြက်သားဒံပေါက်', 'price' => 1100, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A016'],
                ['name' => 'ကဏန်းမဆလာချက်', 'price' => 1000, 'brand_id' => 1, 'categories' => 1, 'stock' => 100, 'code' => 'A017'],
                ['name' => 'အမဲနှပ်ဗူးကြီး', 'price' => 990, 'brand_id' => 2, 'categories' => 1, 'stock' => 100, 'code' => 'A018'],
                ['name' => 'အမဲနှပ်ဗူးသေး', 'price' => 500, 'brand_id' => 2, 'categories' => 1, 'stock' => 100, 'code' => 'A019'],
                ['name' => 'ဆိပ်သားနှပ်', 'price' => 500, 'brand_id' => 2, 'categories' => 1, 'stock' => 100, 'code' => 'A020'],
                ['name' => 'ငါးသလောက်ပေါင်း', 'price' => 530, 'brand_id' => 2, 'categories' => 1, 'stock' => 100, 'code' => 'A021'],
                ['name' => 'ငါးမွှေချက်', 'price' => 400, 'brand_id' => 2, 'categories' => 1, 'stock' => 100, 'code' => 'A022'],
                ['name' => 'ငါးအစပ်ကြော်', 'price' => 430, 'brand_id' => 2, 'categories' => 1, 'stock' => 100, 'code' => 'A023'],
                ['name' => 'သပ်သပ်လွတ်ပဲဟင်း', 'price' => 300, 'brand_id' => 2, 'categories' => 1, 'stock' => 100, 'code' => 'A024'],
                ['name' => 'ငံပြာရည်ချက်', 'price' => 450, 'brand_id' => 2, 'categories' => 1, 'stock' => 100, 'code' => 'A025'],
                ['name' => 'ငါးကြင်းပေါင်း', 'price' => 430, 'brand_id' => 2, 'categories' => 1, 'stock' => 100, 'code' => 'A026'],
                ['name' => 'ငါးချိုချဥ်ကြော်', 'price' => 300, 'brand_id' => 2, 'categories' => 1, 'stock' => 100, 'code' => 'A027'],
                ['name' => 'စတော်ပဲပြုတ်ပေါင်း', 'price' => 830, 'brand_id' => 3, 'categories' => 1, 'stock' => 100, 'code' => 'A028'],
                ['name' => 'သီးစုံပဲကုလားဟင်း', 'price' => 880, 'brand_id' => 3, 'categories' => 1, 'stock' => 100, 'code' => 'A029'],
                ['name' => 'ပုဇွန်တုတ်ငပိရည်ကျို', 'price' => 1350, 'brand_id' => 3, 'categories' => 1, 'stock' => 100, 'code' => 'A030'],
                ['name' => 'ရခိုင်မုန့်တီ', 'price' => 980, 'brand_id' => 3, 'categories' => 1, 'stock' => 100, 'code' => 'A031'],
                ['name' => 'ငါးသေတ္တာ', 'price' => 200, 'brand_id' => 4, 'categories' => 1, 'stock' => 100, 'code' => 'A032'],
                ['name' => 'ဆားဒင်း Chill Sauce', 'price' => 300, 'brand_id' => 4, 'categories' => 1, 'stock' => 100, 'code' => 'A033'],

                ['name' => 'ပင်းတယရှယ်နှယ်', 'price' => 600, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A034'],
                ['name' => 'ပင်ပျိုရွက်နုမိုးကုတ်လက်ဖက်အဆိမ့်', 'price' => 1300, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A035'],
                ['name' => 'ပင်ပျိုရွက်နုမိုးကုတ်လက်ဖက်အစပ်', 'price' => 1300, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A036'],
                ['name' => 'ရှူးရှဲဇယန်းလက်ဖက်', 'price' => 1300, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A037'],
                ['name' => 'စောမိုလက်ဖက်အစိမ့်', 'price' => 1550, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A038'],
                ['name' => 'စောမိုလက်ဖက်အစပ်', 'price' => 1550, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A039'],
                ['name' => 'ရှမ်း‌ရွှေတောင်ရှူးရဲ', 'price' => 1200, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A040'],
                ['name' => 'အဖိုးကြီးသုပ်အစပ်', 'price' => 250, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A041'],
                ['name' => 'ရှူးရုဲ(အတွဲ)', 'price' => 550, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A042'],
                ['name' => 'မတုတ်မ ရှယ်ခုနှစ်ထွေကြော်', 'price' => 1300, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A043'],
                ['name' => 'ဝင်းနှစ်ပြန်ကြော်', 'price' => 1170, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A044'],
                ['name' => 'ပင်ပျိုရွက်နု (ရှယ်ဂျင်းနှပ်)', 'price' => 480, 'brand_id' => 5, 'categories' => 2, 'stock' => 100, 'code' => 'A045'],

                ['name' => 'ပင်ပျိုရွက်နု (ရခိုင်ငါးပိထောင်း)', 'price' => 750, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A046'],
                ['name' => 'ပင်ပျိုရွက်နု (မရမ်းသီးငပိထောင်း)', 'price' => 750, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A047'],
                ['name' => 'ပင်ပျိုရွက်နု (သရက်သီးငပိကြော်)', 'price' => 1300, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A048'],
                ['name' => 'အိမ်ချက်ပုဇွန်တုပ်ငပိရေကျို', 'price' => 1350, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A049'],
                ['name' => 'Best(ငပိချက်)', 'price' => 490, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A050'],
                ['name' => 'Ready (ငံပြာရည်ချက်)', 'price' => 450, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A051'],
                ['name' => 'Best(မရမ်းငပိချက်)', 'price' => 480, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A052'],
                ['name' => 'ဦးမှန်ကြီးငပိရည်ကျို( သုံးမျိုးစပ်)', 'price' => 1650, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A053'],
                ['name' => 'ယောက်ျားကြီးစပ်ပြဲလန်ငပိရည်ကျို', 'price' => 1200, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A054'],
                ['name' => 'ထိုင်းငပိစိမ်းဗူးသေး', 'price' => 330, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A055'],
                ['name' => 'ထိုင်းငပိစိမ်းဗူးကြီး', 'price' => 650, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A056'],
                ['name' => 'ထိုင်းငပိရေကျိုအနှစ်', 'price' => 480, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A057'],
                ['name' => 'မင်းသားကြီးအာပြဲကင်', 'price' => 780, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A058'],
                ['name' => 'မင်းသားကြီးငါးနီတူ', 'price' => 650, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A059'],
                ['name' => 'မင်းသားကြီးငါးတံခွန်ခြောက်', 'price' => 650, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A060'],
                ['name' => 'မင်းသားကြီးအာပြဲဘာလချောင်အစပ်', 'price' => 700, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A061'],
                ['name' => 'မင်းသားကြီးမရမ်းငပိကြော်', 'price' => 700, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A062'],
                ['name' => 'မင်းသားကြီးပုစွန်ခြောက်မှုန့်', 'price' => 780, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A063'],
                ['name' => 'မင်းသားကြီးရွှေပုစွန်ခြောက်', 'price' => 950, 'brand_id' => 6, 'categories' => 3, 'stock' => 100, 'code' => 'A064'],

                ['name' => 'အဲမီးရှမ်းခေါက်ဆွဲ', 'price' => 400, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A065'],
                ['name' => 'အဲမီးရှမ်းတို့ဟူးနွေး', 'price' => 400, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A066'],
                ['name' => 'အဲမီးရှမ်းရည်ပျော်', 'price' => 400, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A067'],
                ['name' => 'အဲမီးရူးရှဲရှမ်း', 'price' => 400, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A068'],
                ['name' => 'အဲမီးရှမ်းကော်ရည်', 'price' => 400, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A069'],
                ['name' => 'အဲမီးမိုးကုပ်မြီးရှည်', 'price' => 400, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A070'],
                ['name' => 'အဲမီးဆန်ပြားခေါက်ဆွဲ', 'price' => 400, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A071'],
                ['name' => 'မင်းလမ်းရခိုင်မုန်တီ', 'price' => 550, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A072'],
                ['name' => 'ပန်းခရေရခိုင်မုန်တီ(1ပွဲစာ)', 'price' => 500, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A073'],
                ['name' => 'ပန်းခ‌ရေအုန်းနို့ခေါက်ဆွဲ', 'price' => 500, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A074'],
                ['name' => 'ကြာပြာ', 'price' => 220, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A075'],
                ['name' => 'မားမား(တုန်ယမ်း)', 'price' => 150, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A076'],
                ['name' => 'Wai wai (မုန့်ဖက်ခြောက်)', 'price' => 350, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A077'],
                ['name' => 'ကွေ့တီယိုမုန့်ဖက်ခြောက်', 'price' => 370, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A078'],
                ['name' => 'မုန့်ဟင်းခါးခြောက်ဖက်', 'price' => 400, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A079'],
                ['name' => 'ကြက်ဥဂျုံခေါက်ဆွဲ', 'price' => 400, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A080'],
                ['name' => 'Top one(ကြက်သားကြာဇံချက်)', 'price' => 950, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A081'],
                ['name' => 'Top one(အုန်းနို့အနှစ်)', 'price' => 1300, 'brand_id' => 7, 'categories' => 4, 'stock' => 100, 'code' => 'A082'],

                ['name' => 'ဦးစပ်ကြီးငရုတ်သီးခွဲခြမ်း', 'price' => 450, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A083'],
                ['name' => 'ဦးစပ်ကြီးငရုတ်သီးအကျက်မှုန့်', 'price' => 350, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A084'],
                ['name' => 'အရောင်တင်မှုန့်', 'price' => 300, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A085'],
                ['name' => 'နနွင်းမှုန့်', 'price' => 300, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A086'],
                ['name' => 'ငရုပ်ကောင်း', 'price' => 320, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A087'],
                ['name' => 'အချိုမှုန့် (အာဂျီနိုမိုကို)', 'price' => 330, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A088'],
                ['name' => 'ထိုင်းငံပြာရည်', 'price' => 270, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A089'],
                ['name' => 'ခနောကြက်သားမှုန့်', 'price' => 580, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A090'],
                ['name' => 'ABC(ကြာညှိ)', 'price' => 620, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A091'],
                ['name' => 'ပဲငံပြာရည်အကျဲ', 'price' => 430, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A092'],
                ['name' => 'ခရုဆီ', 'price' => 480, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A093'],
                ['name' => 'ကုလားလေး(မဆလာ)', 'price' => 200, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A094'],
                ['name' => 'ကုလားကြီး(မဆလာ)', 'price' => 450, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A095'],
                ['name' => 'ပဲငပိခြောက်', 'price' => 500, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A096'],
                ['name' => 'ကုလားပဲခြမ်း', 'price' => 300, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A097'],
                ['name' => 'စားတော်ပဲစိမ်း', 'price' => 350, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A098'],
                ['name' => 'ကြက်သွန်ဖြူကြော်', 'price' => 480, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A099'],
                ['name' => 'Spy(မာလာရှမ်းကောအနှစ်)', 'price' => 800, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A100'],
                ['name' => 'လာရှိုးရှမ်းရှမ်း(မာလာမောက်ချိုင်အနှစ်)', 'price' => 600, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A101'],
                ['name' => 'လာရှိုးရှမ်းရှမ်း(မာလာဟောပေါ့အနှစ်)', 'price' => 600, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A102'],
                ['name' => 'မြင်းနက် ပုန်းရည်ကြီး', 'price' => 400, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A103'],
                ['name' => 'ကွေ့တီယိုအမှုန့်', 'price' => 630, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A104'],
                ['name' => 'အုန်းနို့', 'price' => 680, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A105'],
                ['name' => 'ထိုင်းငပိဘူးသေး', 'price' => 330, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A106'],
                ['name' => 'ထိုင်းငပိဘူးကြီး', 'price' => 650, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A107'],
                ['name' => 'Argar ကျောက်ကျော', 'price' => 350, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A108'],
                ['name' => 'Telephone ကျောက်ကျော', 'price' => 400, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A109'],
                ['name' => 'မျှစ်ပြုတ်', 'price' => 430, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A110'],
                ['name' => 'မျှစ်ချဉ်', 'price' => 430, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A111'],
                ['name' => 'အထောင်းရည်', 'price' => 480, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A112'],
                ['name' => 'မွှေးပဲကျက်မှုန့်', 'price' => 300, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A113'],
                ['name' => 'မွှေးပဲစိမ်းမှုန့်', 'price' => 330, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A114'],
                ['name' => 'မွှေးမုန့်ဟင်းခါးရည်မှုန့်', 'price' => 330, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A115'],
                ['name' => 'မွှေးကုလားပဲဘယာမှုန့်', 'price' => 330, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A116'],
                ['name' => 'မွှေးရွှေလိပ်ပြာအ‌ကြော်မှုန့်', 'price' => 330, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A117'],
                ['name' => 'မွှေးဆန်အစိမ်းမှုန့်စစ်စစ်', 'price' => 330, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A118'],
                ['name' => 'လက်ပံခြောက်', 'price' => 490, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A119'],
                ['name' => 'သရက်သီးခြောက်', 'price' => 490, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A120'],
                ['name' => 'သခွပ်ပွင့်ခြောက်', 'price' => 490, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A121'],
                ['name' => 'ကုလားပဲခြမ်း', 'price' => 300, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A123'],
                ['name' => 'စတော်ပဲစိမ်း', 'price' => 350, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A124'],
                ['name' => 'ကြူကြူမွှေး', 'price' => 300, 'brand_id' => 8, 'categories' => 5, 'stock' => 100, 'code' => 'A125'],

                ['name' => 'ရွှေပြည်နန်း သနပ်ခါးသံပုရာ', 'price' => 300, 'brand_id' => 9, 'categories' => 5, 'stock' => 100, 'code' => 'A126'],
                ['name' => 'ရွှေပြည်နန်း သနပ်ခါးရှင်မတောင်', 'price' => 300, 'brand_id' => 9, 'categories' => 5, 'stock' => 100, 'code' => 'A127'],
                ['name' => 'ရွှေပြည်နန်း သနပ်ခါးကံ့ကော်', 'price' => 300, 'brand_id' => 9, 'categories' => 5, 'stock' => 100, 'code' => 'A128'],
                ['name' => 'ဦးသိန်းမောင်(တြိရတနာ)', 'price' => 650, 'brand_id' => 9, 'categories' => 5, 'stock' => 100, 'code' => 'A129'],
                ['name' => 'NIVEA (For Men) (ဂျိူင်းလိမ်းဆီ)', 'price' => 830, 'brand_id' => 9, 'categories' => 5, 'stock' => 100, 'code' => 'A130'],
                ['name' => 'Healthy Shop(နှုတ်ခမ်းနီ)', 'price' => 1000, 'brand_id' => 9, 'categories' => 5, 'stock' => 100, 'code' => 'A131'],
                ['name' => 'Epsoft +(1ဗူး)', 'price' => 2000, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A132'],
                ['name' => 'Epsoft +(1ကတ်)', 'price' => 700, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A133'],

                ['name' => 'မယ်ခွေ(ဆေးလိပ်)', 'price' => 380, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A134'],
                ['name' => 'ကွမ်းပေါင်း', 'price' => 100, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A135'],
                ['name' => 'နနွင်းလုံး', 'price' => 500, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A136'],
                ['name' => 'အဆီကျဆေးမြတ်သုခ', 'price' => 450, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A137'],
                ['name' => 'ထွန်းရွှေဝါ(အကြောလိမ်းဆေး)', 'price' => 480, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A138'],
                ['name' => 'လင်ဇီးအကြောလိမ်းဆေး', 'price' => 480, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A139'],
                ['name' => 'မဟာဖြေဆေး', 'price' => 470, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A140'],
                ['name' => 'ရွှေဟင်္သာ(သံမနိုင်ကျောက်မနိုင်)', 'price' => 470, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A141'],
                ['name' => 'နဂါးနိုင်(လျက်စား)', 'price' => 550, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A142'],
                ['name' => 'ရှမ်းပျိုမယ်(သွေးဆေး)', 'price' => 400, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A143'],
                ['name' => 'အောင်တံခွန်အစောကြေလေနိုင်ဆေး', 'price' => 400, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A144'],
                ['name' => 'ကေသီပန်(သွေးပုပ်ချဆေး)', 'price' => 450, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A145'],
                ['name' => 'Paracetamol(ကိုယ်ပူကျအကိုက်ခဲပျောက်ဆေး)', 'price' => 250, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A146'],
                ['name' => 'Oramin-F(1ဗူး)', 'price' => 2100, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A147'],
                ['name' => 'Oramin-F(1 ကတ်)', 'price' => 400, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A148'],
                ['name' => 'Decolgen(အအေးမိပျောက်ဆေး)', 'price' => 250, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A206'],
                ['name' => 'Air-X(လေဆေး)', 'price' => 200, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A149'],
                ['name' => 'Oramin-G(1ဗူး)', 'price' => 2100, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A150'],
                ['name' => 'Oramin-G(1 ကတ်)', 'price' => 400, 'brand_id' => 9, 'categories' => 6, 'stock' => 100, 'code' => 'A151'],

                ['code' => 'A152', 'name' => 'အရသာကောင်းမြေပဲချိုချဥ်', 'price' => 980, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A153', 'name' => 'အရသာကောင်းနှမ်းမြေပဲယို', 'price' => 880, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A154', 'name' => 'ရွှေပုဇွန် နံကထိုင်', 'price' => 980, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A155', 'name' => 'ကျောက်ဖရုံယို', 'price' => 470, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A156', 'name' => 'သရက်ပြင်', 'price' => 470, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A157', 'name' => 'Sakura ဇီးယိုအစပ်', 'price' => 350, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A158', 'name' => 'Sakura ဇီးယိုအချို', 'price' => 350, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A159', 'name' => 'Sakura ဇီးစပ်', 'price' => 350, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A160', 'name' => 'ရွှေကျားပျံ မန်ကျည်းလိပ်', 'price' => 380, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A161', 'name' => 'မယ်ဝ ရှောက်သံ', 'price' => 350, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A162', 'name' => 'မယ်ဝ ဇီးမဆလာ', 'price' => 350, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A163', 'name' => 'ရှယ်ပြုံးမရမ်းမဆလာ', 'price' => 400, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A164', 'name' => 'မရမ်းကြွပ်ချဉ်စပ်သုပ် ဘရဏီ', 'price' => 1100, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A165', 'name' => 'သီးစုံရှူးရှဲယို ဘရဏီ', 'price' => 1000, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],
                ['code' => 'A166', 'name' => 'ရွှေမင်းသမီးသရက်သား', 'price' => 400, 'brand_id' => 9, 'categories' => 7, 'stock' => 1],

                ['code' => 'A167', 'name' => 'ထန်းတော ဝက်မွခြောက်စပ်မွှေး', 'price' => 850, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A168', 'name' => 'ထန်းတော ကြက်မွခြောက်စပ်မွှေး', 'price' => 850, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A169', 'name' => 'ထန်းတော အမဲမွခြောက်စပ်မွှေး', 'price' => 850, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A170', 'name' => 'ထန်းတော ငါးကျည်းကျပ်တိုက်ချဉ်စပ်ကြော်', 'price' => 850, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A171', 'name' => 'Tomo သပ်သပ်လွတ်မြေပဲငရုတ်သီးကြော်', 'price' => 750, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A172', 'name' => 'Tomo ငါးကျည်းကျပ်တိုက်ကြော်', 'price' => 830, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A173', 'name' => 'Tomo ရခိုင်ငပိကြော်', 'price' => 1200, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A174', 'name' => 'ဖျာပုံ ပုရစ်ကြော်', 'price' => 1380, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A175', 'name' => 'ယောက်ျားကြီး စပ်ပြဲလန်', 'price' => 1200, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A176', 'name' => 'ဦးမှန်ကြီး သုံးမျိုးစပ် ငပိရည်ကျို', 'price' => 1650, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A177', 'name' => 'ရွှေကျင် ငရံ့မီးဖုတ်အပျော့ကင်', 'price' => 680, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A178', 'name' => 'ထိမိဂွိ အစပ်', 'price' => 420, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A179', 'name' => 'ထိမိဂွိ ပူစပ်', 'price' => 420, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A180', 'name' => 'ပင်ပျိုရွက်နု ဂွေးသီးငပိကြော်', 'price' => 1300, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A181', 'name' => 'ပင်ပျိုရွက်နု ရှမ်းပဲပုတ်ကြော်', 'price' => 650, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A182', 'name' => 'ပင်ပျိုရွက်နု သရက်သီး ငါးပိကြော်', 'price' => 1300, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A183', 'name' => 'သွေးမလေး ငရုတ်သီးဆားလှော်ထောင်း', 'price' => 1050, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A184', 'name' => 'မယ်ဒိုင် အင်းပုစွန်ကြော်', 'price' => 570, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A185', 'name' => 'Tai lon ငရုတ်သီးနှမ်းကပ်ကြော်', 'price' => 700, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A186', 'name' => 'Star flower cookie', 'price' => 650, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A187', 'name' => 'ရသာမွန် ဆီတို့ဖူး', 'price' => 310, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A188', 'name' => 'ရွှေပုစွန် cookie', 'price' => 550, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A189', 'name' => 'ရှူးရှဲ ဇယန်းလက်ဖက်', 'price' => 1300, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A190', 'name' => 'တော့ပ်ဝမ်း ရန်ကုန်ထမင်းသုပ် အနှစ်ထုတ်', 'price' => 750, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A191', 'name' => 'တော့ပ်ဝမ်း ကြက်သားကြာဇံချက် အနှစ်ထုတ်', 'price' => 1000, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A192', 'name' => 'တော့ပ်ဝမ်း ကြက်သားအုန်းနို့ခေါက်ဆွဲ အနှစ်ထုတ်', 'price' => 1250, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A193', 'name' => 'ချင်းရိုးရာ ဆာဗူးသီး', 'price' => 720, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A194', 'name' => 'လားရှိုး(ရှမ်းရှမ်း)မာလာဟော့ပေါ့အနှစ်', 'price' => 600, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A195', 'name' => 'လားရှိုး(ရှမ်းရှမ်း)မောက်ချိုက်မာလာထုတ်ထိုးအနှစ်', 'price' => 600, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A196', 'name' => 'ပင်ပျိုရွက်နုနှမ်းလှော်', 'price' => 500, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A197', 'name' => 'ခင်ထွေးရီ မုန့်ဟင်းခါးခြောက်', 'price' => 1100, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A198', 'name' => 'Pann Kha Yayမုန်လာပင်ပေါက်ချဉ်ရည်ဟင်း', 'price' => 350, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A199', 'name' => 'Pann Kha Yay ဘူးသီးပုတက်စာဟင်းခါး', 'price' => 350, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A200', 'name' => 'Pann Kha Yay အညာဟင်းရွက်စုံဟင်းချို', 'price' => 350, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A201', 'name' => 'Pann Kha Yay ကင်ပွန်းရွက်ပုတက်စာဟင်းချို', 'price' => 350, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A202', 'name' => 'ရွှေတို့ရွာ နှမ်းဖက်ချဉ်', 'price' => 600, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A203', 'name' => 'ရွှေတို့ရွာ ခံတက်ချဉ်ရွက်', 'price' => 600, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A204', 'name' => 'ရွှေပန်းပွင့်ချဉ်စော်ကားသီး', 'price' => 150, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],
                ['code' => 'A205', 'name' => 'White naviea', 'price' => 830, 'brand_id' => 10, 'categories' => 8, 'stock' => 1],

            ];

            $currency = Currency::where('code', 'MMK')->first();

            $exchange = new Exchange;
            $exchange->currency_id = $currency->id;
            $exchange->rate = '4500';
            $exchange->seller_id = $user['id'];
            $exchange->tenant_id = $tenant->id;
            $exchange->save();

            $datas = [];
            foreach ($products as $index => $product) {
                $attId = Attribute::where('seller_id', $user['id'])->get()->random()->id;
                $data = [
                    'seller_id' => $user['id'],
                    'tenant_id' => $tenant->id,
                    'exchange_id' => $exchange->id,
                    'name' => $product['name'],
                    'buy_price' => $product['price'] - $product['price'] / 100 * 10,
                    'price' => $product['price'],
                    'brand_id' => $product['brand_id'],
                    'mmk_buy_price' => $product['price'] - $product['price'] / 100 * 10,
                    'mmk_price' => $product['price'],
                    'discount_price' => 0,
                    'mmk_discount_price' => $product['price'] - $product['price'] / 100 * 10,
                    'category_id' => $product['categories'],
                    'sku' => $product['code'],
                    'quantity' => $product['stock'],
                    'description' => fake()->paragraph(),
                    'has_variant' => false,
                    'is_public' => fake()->boolean(),
                    'is_recommended' => fake()->boolean(),
                    'is_whole_sale' => fake()->boolean(),
                    'attributes' => json_encode([
                        ['attribute_id' => $attId, 'attribute_value_id' => [AttributeValue::where('attribute_id', $attId)->get()->random()->id]],
                    ]),
                ];

                array_push($datas, $data);
            }
            DB::table('products')->insert($datas);

            DB::commit();
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Create delivery data for the seller
     */
    private function createDeliveries($sellerId, $tenantId)
    {
        // Get all regions and townships
        $regions = Region::with('townships')->get();

        // Sample delivery data for different regions and townships
        $deliveryData = [
            // Yangon Region deliveries
            'ရန်ကုန်တိုင်းဒေသကြီး' => [
                'လှိုင်' => ['fees' => 1500, 'is_cod' => true, 'is_pick_up' => true, 'remark' => 'Same day delivery available'],
                'ရန်ကင်း' => ['fees' => 1500, 'is_cod' => true, 'is_pick_up' => true, 'remark' => ''],
                'တာမွေ' => ['fees' => 1500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'မင်္ဂလာဒုံ' => ['fees' => 2000, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'သင်္ဃန်းကျွန်း' => ['fees' => 2000, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'အမရပူရ' => ['fees' => 1800, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'ထောက်ကြင်း' => ['fees' => 1500, 'is_cod' => true, 'is_pick_up' => true, 'remark' => ''],
                'လတ်ပတ်တန်း' => ['fees' => 1500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'ကမာရွတ်' => ['fees' => 1500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'ဗဟန်း' => ['fees' => 1500, 'is_cod' => true, 'is_pick_up' => true, 'remark' => ''],
                'ကြည့်မြင်တိုင်' => ['fees' => 1500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'လမ်းမတော်' => ['fees' => 1500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'ပုဇွန်တောင်' => ['fees' => 1500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'လင်း' => ['fees' => 3000, 'is_cod' => false, 'is_pick_up' => false, 'remark' => 'Remote area'],
                'ကောက်တန်း' => ['fees' => 2500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
            ],
            // Mandalay Region deliveries
            'မန္တလေးတိုင်းဒေသကြီး' => [
                'အမရပူရ' => ['fees' => 2500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'ချမ်းအေးသာစံ' => ['fees' => 2500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'ချမ်းမြသာစည်' => ['fees' => 2500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'မဟာအောင်မြေ' => ['fees' => 2500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'ပြင်ဦးလွင်' => ['fees' => 2500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'မန္တလေး' => ['fees' => 2500, 'is_cod' => true, 'is_pick_up' => true, 'remark' => ''],
                'ပျော်ဘွယ်' => ['fees' => 3000, 'is_cod' => false, 'is_pick_up' => false, 'remark' => ''],
                'ကျောက်ပန်းတောင်း' => ['fees' => 3500, 'is_cod' => false, 'is_pick_up' => false, 'remark' => 'Remote area'],
                'စဉ့်ကိုင်' => ['fees' => 3000, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'တပ်ကုန်း' => ['fees' => 3000, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
            ],
            // Other regions with sample data
            'ဧရာဝတီတိုင်းဒေသကြီး' => [
                'ပုသိမ်' => ['fees' => 4000, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'မြောင်းမြ' => ['fees' => 3500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'ပန်းတနော်' => ['fees' => 4500, 'is_cod' => false, 'is_pick_up' => false, 'remark' => 'Remote area'],
                'ဟင်္သာတ' => ['fees' => 3500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
            ],
            'မွန်ပြည်နယ်' => [
                'မော်လမြိုင်' => ['fees' => 3000, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'ရေး' => ['fees' => 3500, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
                'သထုံ' => ['fees' => 3000, 'is_cod' => true, 'is_pick_up' => false, 'remark' => ''],
            ],
        ];

        foreach ($regions as $region) {
            // Check if we have delivery data for this region
            $regionName = $region->mm_name;
            if (! isset($deliveryData[$regionName])) {
                continue;
            }

            foreach ($region->townships as $township) {
                $townshipName = $township->mm_name;

                // Check if we have delivery data for this township
                if (! isset($deliveryData[$regionName][$townshipName])) {
                    continue;
                }

                $delivery = $deliveryData[$regionName][$townshipName];

                // Create delivery record
                Delivery::create([
                    'seller_id' => $sellerId,
                    'tenant_id' => $tenantId,
                    'region_id' => $region->id,
                    'township_id' => $township->id,
                    'fees' => $delivery['fees'],
                    'is_cod' => $delivery['is_cod'],
                    'is_pick_up' => $delivery['is_pick_up'],
                    'remark' => $delivery['remark'],
                ]);
            }
        }
    }
}
