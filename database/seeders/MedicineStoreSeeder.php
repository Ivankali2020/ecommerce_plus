<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helper\AttributeType;

class MedicineStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try{

            /// for medicine store
            $medicine = Seller::factory(1)
                ->state([
                    'email' => 'medicine@gmail.com',
                    'name' => 'AB Pharmacy',
                    'phone' => '09 454949610',
                    'description' => 'ဆေးဆိုင်'
                ])
                // ->has(Attribute::factory(3)->has(AttributeValue::factory(3), 'values'))
                // ->has(Payment::factory(5)->has(PaymentAccount::factory(2), 'accounts'))
                ->create()->toArray()[0];

                $attributes = [
                    [
                        'id' => 1,
                        'name' => 'Dosage Form',
                        'values' => [
                            ['id' => 1, 'value' => 'Tablet'],
                            ['id' => 2, 'value' => 'Capsule'],
                            ['id' => 3, 'value' => 'Syrup'],
                            ['id' => 4, 'value' => 'Injection'],
                            ['id' => 5, 'value' => 'Cream'],
                            ['id' => 6, 'value' => 'Gel']
                        ]
                    ],
                    [
                        'id' => 2,
                        'name' => 'Strength',
                        'values' => [
                            ['id' => 7, 'value' => '250mg'],
                            ['id' => 8, 'value' => '500mg'],
                            ['id' => 9, 'value' => '1000mg'],
                            ['id' => 10, 'value' => '5mg'],
                            ['id' => 11, 'value' => '10mg'],
                            ['id' => 12, 'value' => '50mg'],
                            ['id' => 13, 'value' => '100mg']
                        ]
                    ],
                    [
                        'id' => 3,
                        'name' => 'Manufacturer',
                        'values' => [
                            ['id' => 14, 'value' => 'Pfizer'],
                            ['id' => 15, 'value' => 'GlaxoSmithKline'],
                            ['id' => 16, 'value' => 'Novartis'],
                            ['id' => 17, 'value' => 'Johnson & Johnson'],
                            ['id' => 18, 'value' => 'Roche'],
                            ['id' => 19, 'value' => 'Sanofi'],
                            ['id' => 20, 'value' => 'Bayer']
                        ]
                    ],
                    [
                        'id' => 4,
                        'name' => 'Packaging',
                        'values' => [
                            ['id' => 21, 'value' => 'Blister Pack'],
                            ['id' => 22, 'value' => 'Bottle'],
                            ['id' => 23, 'value' => 'Vial'],
                            ['id' => 24, 'value' => 'Sachet']
                        ]
                    ],
                    [
                        'id' => 5,
                        'name' => 'Prescription Required',
                        'values' => [
                            ['id' => 25, 'value' => 'Yes'],
                            ['id' => 26, 'value' => 'No']
                        ]
                    ],
                    [
                        'id' => 6,
                        'name' => 'Route of Administration',
                        'values' => [
                            ['id' => 27, 'value' => 'Oral'],
                            ['id' => 28, 'value' => 'Topical'],
                            ['id' => 29, 'value' => 'Injection'],
                            ['id' => 30, 'value' => 'Sublingual'],
                            ['id' => 31, 'value' => 'Intravenous']
                        ]
                    ],
                    [
                        'id' => 7,
                        'name' => 'Active Ingredient',
                        'values' => [
                            ['id' => 32, 'value' => 'Paracetamol'],
                            ['id' => 33, 'value' => 'Ibuprofen'],
                            ['id' => 34, 'value' => 'Amoxicillin'],
                            ['id' => 35, 'value' => 'Ciprofloxacin'],
                            ['id' => 36, 'value' => 'Metformin'],
                            ['id' => 37, 'value' => 'Lisinopril']
                        ]
                    ],
                    [
                        'id' => 8,
                        'name' => 'Storage Conditions',
                        'values' => [
                            ['id' => 38, 'value' => 'Store below 25°C'],
                            ['id' => 39, 'value' => 'Keep refrigerated'],
                            ['id' => 40, 'value' => 'Protect from light'],
                            ['id' => 41, 'value' => 'Keep in original packaging']
                        ]
                    ],
                    [
                        'id' => 9,
                        'name' => 'Category',
                        'values' => [
                            ['id' => 42, 'value' => 'Pain Reliever'],
                            ['id' => 43, 'value' => 'Antibiotic'],
                            ['id' => 44, 'value' => 'Antidepressant'],
                            ['id' => 45, 'value' => 'Antihypertensive'],
                            ['id' => 46, 'value' => 'Diabetes Medication']
                        ]
                    ],
                    [
                        'id' => 10,
                        'name' => 'Side Effects',
                        'values' => [
                            ['id' => 47, 'value' => 'Nausea'],
                            ['id' => 48, 'value' => 'Dizziness'],
                            ['id' => 49, 'value' => 'Drowsiness'],
                            ['id' => 50, 'value' => 'Headache'],
                            ['id' => 51, 'value' => 'Allergic Reaction']
                        ]
                    ]
                ];
                
                foreach ($attributes as $att){
                    $attribute = new Attribute();
                    $attribute->name = $att['name'];
                    $attribute->type = AttributeType::Text;
                    $attribute->seller_id = $medicine['id'];
                    $attribute->save();
                    foreach ($att['values'] as $val){
                        $attributeValue = new AttributeValue();
                        $attributeValue->attribute_id = $attribute->id;
                        $attributeValue->value = $val['value'];
                        $attributeValue->save();
                    }
                }

            $medicinCategories = json_decode(file_get_contents(public_path('/assets/medicineCategories.json')), true);

            foreach ($medicinCategories as $cat){
                $category = new Category();
                $category->seller_id = $medicine['id'];
                $category->name = $cat['label'];
                $category->save();
                foreach ($cat['children'] as $child){
                    $sub = new Category();
                    $sub->parent_id = $category->id;
                    $sub->seller_id = $medicine['id'];
                    $sub->name = $child['label'];
                    $sub->save();
                }
            }

            $medicineBrands = [
                'ensure',
            ];

            foreach ($medicineBrands as $cat){
                $brand = new Brand();
                $brand->name = $cat;
                $brand->seller_id = $medicine['id'];
                $brand->save();
//                $brand->addMediaFromUrl(fake()->imageUrl(width: 200,height: 200,randomize: false,word: 'Brand',gray: true))
//                ->toMediaCollection('logos');
            }

            $products  = [
                "Paracetamol" => [
                    "description" => "A common over-the-counter pain reliever and fever reducer.",
                    "usage" => "Used to treat mild to moderate pain and reduce fever."
                ],
                "Ibuprofen" => [
                    "description" => "A nonsteroidal anti-inflammatory drug (NSAID).",
                    "usage" => "Used to reduce inflammation, pain, and fever."
                ],
                "Aspirin" => [
                    "description" => "A salicylate used as an analgesic, anti-inflammatory, and antipyretic.",
                    "usage" => "Used for pain relief, fever reduction, and to reduce the risk of heart attacks."
                ],
                "Amoxicillin" => [
                    "description" => "An antibiotic used to treat various bacterial infections.",
                    "usage" => "Used to treat infections like ear infections, strep throat, and urinary tract infections."
                ],
                "Ciprofloxacin" => [
                    "description" => "A fluoroquinolone antibiotic that fights bacteria.",
                    "usage" => "Used to treat a variety of bacterial infections, including urinary tract infections and respiratory infections."
                ],
                "Loratadine" => [
                    "description" => "An antihistamine used to relieve allergy symptoms.",
                    "usage" => "Used to treat hay fever, rhinitis, and urticaria (hives)."
                ],
                "Metformin" => [
                    "description" => "A medication used to treat type 2 diabetes.",
                    "usage" => "Used to improve blood sugar control in patients with type 2 diabetes."
                ],
                "Omeprazole" => [
                    "description" => "A proton pump inhibitor (PPI) used to reduce stomach acid.",
                    "usage" => "Used to treat gastroesophageal reflux disease (GERD), ulcers, and heartburn."
                ],
                "Levothyroxine" => [
                    "description" => "A synthetic form of the thyroid hormone used to treat hypothyroidism.",
                    "usage" => "Used to replace or supplement low thyroid hormone levels in patients with hypothyroidism."
                ],
                "Atorvastatin" => [
                    "description" => "A statin used to lower cholesterol levels.",
                    "usage" => "Used to reduce the risk of heart disease and stroke by lowering cholesterol levels."
                ],
                "Hydrochlorothiazide" => [
                    "description" => "A thiazide diuretic used to reduce blood pressure and swelling.",
                    "usage" => "Used to treat high blood pressure and edema (fluid retention)."
                ],
                "Prednisone" => [
                    "description" => "A corticosteroid used to treat inflammation and immune system disorders.",
                    "usage" => "Used to treat conditions like arthritis, allergies, and skin conditions."
                ],
                "Clopidogrel" => [
                    "description" => "An antiplatelet medication that prevents blood clots.",
                    "usage" => "Used to prevent heart attacks, strokes, and other cardiovascular events."
                ],
                "Losartan" => [
                    "description" => "An angiotensin II receptor antagonist used to treat high blood pressure.",
                    "usage" => "Used to treat high blood pressure and protect the kidneys in patients with diabetes."
                ],
                "Simvastatin" => [
                    "description" => "A statin used to lower cholesterol levels.",
                    "usage" => "Used to lower cholesterol levels and reduce the risk of heart disease."
                ],
                "Zoloft" => [
                    "description" => "A selective serotonin reuptake inhibitor (SSRI) used to treat depression and anxiety.",
                    "usage" => "Used to treat depression, anxiety, OCD, and PTSD."
                ],
                "Prozac" => [
                    "description" => "An SSRI used to treat depression, anxiety, and related disorders.",
                    "usage" => "Used to treat depression, anxiety, and panic disorders."
                ],
                "Alprazolam" => [
                    "description" => "A benzodiazepine used to treat anxiety and panic disorders.",
                    "usage" => "Used to treat anxiety, panic attacks, and short-term anxiety symptoms."
                ],
                "Diazepam" => [
                    "description" => "A benzodiazepine used to treat anxiety, muscle spasms, and seizures.",
                    "usage" => "Used to treat anxiety, muscle spasms, and seizures."
                ],
                "Citalopram" => [
                    "description" => "An SSRI used to treat depression.",
                    "usage" => "Used to treat depression and anxiety disorders."
                ],
                "Furosemide" => [
                    "description" => "A loop diuretic used to reduce fluid buildup in the body.",
                    "usage" => "Used to treat edema and high blood pressure."
                ],
                "Gabapentin" => [
                    "description" => "A medication used to treat nerve pain and seizures.",
                    "usage" => "Used to treat nerve pain and prevent seizures."
                ],
                "Carbamazepine" => [
                    "description" => "An anticonvulsant used to treat seizures and nerve pain.",
                    "usage" => "Used to treat epilepsy, nerve pain, and bipolar disorder."
                ],
                "Tramadol" => [
                    "description" => "A pain reliever used to treat moderate to severe pain.",
                    "usage" => "Used to treat moderate to severe pain."
                ],
                "Fexofenadine" => [
                    "description" => "An antihistamine used to relieve allergy symptoms.",
                    "usage" => "Used to treat hay fever, allergic rhinitis, and hives."
                ],
                "Ranitidine" => [
                    "description" => "A medication used to reduce stomach acid production.",
                    "usage" => "Used to treat GERD, ulcers, and indigestion."
                ],
                "Tamsulosin" => [
                    "description" => "A medication used to treat enlarged prostate symptoms.",
                    "usage" => "Used to treat symptoms of benign prostatic hyperplasia (BPH)."
                ],
                "Ezetimibe" => [
                    "description" => "A medication used to lower cholesterol levels.",
                    "usage" => "Used to lower cholesterol levels in patients with hypercholesterolemia."
                ],
                "Doxycycline" => [
                    "description" => "An antibiotic used to treat a wide range of bacterial infections.",
                    "usage" => "Used to treat infections such as respiratory infections and acne."
                ],
                "Lisinopril" => [
                    "description" => "An ACE inhibitor used to treat high blood pressure.",
                    "usage" => "Used to treat high blood pressure and heart failure."
                ],
                "Cetirizine" => [
                    "description" => "An antihistamine used to treat allergy symptoms.",
                    "usage" => "Used to treat hay fever, rhinitis, and itching caused by hives."
                ],
                "Hydroxyzine" => [
                    "description" => "An antihistamine used to treat anxiety and allergy symptoms.",
                    "usage" => "Used to treat anxiety, nausea, and allergic reactions."
                ],
                "Clindamycin" => [
                    "description" => "An antibiotic used to treat bacterial infections.",
                    "usage" => "Used to treat infections such as respiratory infections and skin infections."
                ],
                "Azithromycin" => [
                    "description" => "An antibiotic used to treat various bacterial infections.",
                    "usage" => "Used to treat respiratory infections, skin infections, and sexually transmitted infections."
                ],
                "Amoxiclav" => [
                    "description" => "A combination of amoxicillin and clavulanic acid, used to treat bacterial infections.",
                    "usage" => "Used to treat infections like ear infections, respiratory infections, and urinary tract infections."
                ],
                "Metronidazole" => [
                    "description" => "An antibiotic used to treat bacterial and parasitic infections.",
                    "usage" => "Used to treat infections like bacterial vaginosis and giardiasis."
                ],
                "Tetracycline" => [
                    "description" => "An antibiotic used to treat a variety of bacterial infections.",
                    "usage" => "Used to treat acne, respiratory infections, and urinary tract infections."
                ],
                "Sildenafil" => [
                    "description" => "A medication used to treat erectile dysfunction.",
                    "usage" => "Used to treat erectile dysfunction and pulmonary arterial hypertension."
                ],
                "Methylprednisolone" => [
                    "description" => "A corticosteroid used to treat inflammation and immune disorders.",
                    "usage" => "Used to treat inflammation, allergies, and autoimmune disorders."
                ],
                "Chloroquine" => [
                    "description" => "An antimalarial medication used to treat malaria.",
                    "usage" => "Used to prevent and treat malaria."
                ],
                "Methotrexate" => [
                    "description" => "A medication used to treat cancer and autoimmune diseases.",
                    "usage" => "Used to treat cancer, rheumatoid arthritis, and psoriasis."
                ],
                "Duloxetine" => [
                    "description" => "An SNRI used to treat depression and anxiety.",
                    "usage" => "Used to treat depression, anxiety, and pain related to conditions like fibromyalgia."
                ],
                "Aripiprazole" => [
                    "description" => "An antipsychotic used to treat schizophrenia and bipolar disorder.",
                    "usage" => "Used to treat schizophrenia, bipolar disorder, and major depressive disorder."
                ],
                "Escitalopram" => [
                    "description" => "An SSRI used to treat depression and anxiety.",
                    "usage" => "Used to treat depression, generalized anxiety disorder, and OCD."
                ],
                "Sertraline" => [
                    "description" => "An SSRI used to treat depression and anxiety.",
                    "usage" => "Used to treat depression, anxiety, and panic disorder."
                ],
                "Bupropion" => [
                    "description" => "An atypical antidepressant used to treat depression and smoking cessation.",
                    "usage" => "Used to treat depression, seasonal affective disorder, and help with smoking cessation."
                ],
                "Naproxen" => [
                    "description" => "A nonsteroidal anti-inflammatory drug (NSAID) used to reduce pain and inflammation.",
                    "usage" => "Used to treat pain, inflammation, and fever."
                ],
                "Diphenhydramine" => [
                    "description" => "An antihistamine used to treat allergies and insomnia.",
                    "usage" => "Used to treat allergic reactions and as a short-term sleep aid."
                ],
                "Codeine" => [
                    "description" => "A narcotic used to treat pain and cough.",
                    "usage" => "Used to treat mild to moderate pain and as a cough suppressant."
                ],
                "Fluoxetine" => [
                    "description" => "An SSRI used to treat depression, anxiety, and OCD.",
                    "usage" => "Used to treat depression, anxiety, and obsessive-compulsive disorder."
                ]
            ];

            $currency = Currency::where('code','MMK')->first();

            $exchange = new Exchange();
            $exchange->currency_id = $currency->id;
            $exchange->rate = '4500';
            $exchange->seller_id = $medicine['id'];
            $exchange->save();

            $datas = [];
            foreach ($products as $index=>$product){
                $attId = Attribute::where('seller_id',$medicine['id'])->get()->random()->id;
                $price = 1000 * (random_int(0,5));
                $data = [
                    'seller_id' => $medicine['id'],
                    'exchange_id' => $exchange->id,
                    'name' => $index,
                    'buy_price' => $price / 100 * 10 ,
                    'price' => $price,
                    'mmk_buy_price' => $price / 100 * 10 ,
                    'mmk_price' => $price,
                    'discount_price' => $price / 100 * 10,
                    'mmk_discount_price' => $price / 100 * 10,
                    'brand_id' => null,
                    'category_id' => Category::where('seller_id',$medicine['id'])->get()->random()->id,
                    'sku' => uniqid(),
                    'quantity' => fake()->randomDigitNotNull,
                    'description' => $product['description'] . $product['usage'],
                    'has_variant' => false,
                    'is_public' => fake()->boolean(),
                    'is_recommended' => fake()->boolean(),
                    'is_whole_sale' => fake()->boolean(),
                    'attributes' =>  json_encode([
                        ['attribute_id' => $attId, 'attribute_value_id' => [
                            AttributeValue::where('attribute_id',$attId)->get()->random()->id
                        ],]
                    ]),
                ];

                array_push($datas, $data);

            }
            DB::table('products')->insert($datas);
            DB::commit();

        }catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
