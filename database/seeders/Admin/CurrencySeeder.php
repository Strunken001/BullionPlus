<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = array(
            array(
                'id' => '1',
                'admin_id' => '1',
                'country' => 'United States',
                'name' => 'United States dollar',
                'code' => 'USD',
                'symbol' => '$',
                'flag' => '1e4551f9-2216-4fcc-83b3-3a9b85c5c379.webp',
                'default' => 1
            )
        );

        Currency::insert($currencies);
        // Currency::factory()->times(50)->create();
    }
}
