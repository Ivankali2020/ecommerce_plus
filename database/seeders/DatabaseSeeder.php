<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Region;
use App\Models\Seller;
use App\Models\Township;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $regions = json_decode(file_get_contents(public_path('assets/db.json')));


        foreach ($regions->data as $region){

            $reg = new Region();
            $reg->mm_name = $region->mm;
            $reg->en_name = $region->eng;
            $reg->save();
            foreach ($region->districts as $district){

                foreach ($district->townships as $township){
                    $tw = new Township();
                    $tw->mm_name = $township->mm;
                    $tw->en_name = $township->eng;
                    $tw->region_id = $reg->id;
                    $tw->save();
                }

            }
        }

        $this->call([
            AdminSeeder::class,
            CurrencySeeder::class,
            // ShieldSeeder::class,
            MultiStoreSeeder::class,
            MedicineStoreSeeder::class,
            SubscriptionPlanSeeder::class
        ]);


//         \App\Models\User::factory(10)->create();


    }
}
