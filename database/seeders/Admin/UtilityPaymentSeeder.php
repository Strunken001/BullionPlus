<?php

namespace Database\Seeders;

use App\Models\Admin\ReloadlyApi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UtilityPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $utility_payment_apis = array(
            array(
                'provider' => 'RELOADLY',
                'type' => 'UTILITY-PAYMENT',
                'credentials' => '{"client_id":"0Tac1xfwuxvloESuakKYpD34fYCJtqVB","secret_key":"p16MBCEYpy-9KJaYLzAhe7Ux80YmXx-GDXSiXjz9JjBU9EBC8hprAjDNTcHjSp7","production_base_url":"https://utilities.reloadly.com","sandbox_base_url":"https://utilities-sandbox.reloadly.com"}',
                'status' => '1',
                'env' => 'sandbox',
                'created_at' => now(),
                'updated_at' => now()
            )
        );

        ReloadlyApi::insert($utility_payment_apis);
    }
}
