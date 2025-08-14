<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = json_decode(file_get_contents(public_path('assets/currencies.json')));

        foreach ($currencies as $cur) {

            $currency = new Currency;
            $currency->code = $cur->code;
            $currency->name = $cur->name;
            $currency->name_plural = $cur->name_plural;
            $currency->symbol = $cur->symbol;
            $currency->symbol_native = $cur->symbol_native;
            $currency->save();

        }
    }
}
