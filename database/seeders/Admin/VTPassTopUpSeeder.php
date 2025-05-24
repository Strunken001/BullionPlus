<?php

namespace Database\Seeders\Admin;

use App\Models\VTPassApi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VTPassTopUpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mobile_topup = array(
            array(
                'provider' => 'VTPASS',
                'type' => 'MOBILE-TOPUP',
                'credentials' => '{"api_key":"424ea5ac09f6f3719d0f1c4d9d51762f","public_key":"PK_8636563ccf8a29563d5eb9a6bdf5baac1a5ffd7b191","secret_key":"SK_1080894c52ed047446ea8bda576193f40e7d784d4fc","production_base_url":"https://vtpass.com","sandbox_base_url":"https://sandbox.vtpass.com"}',
                'status' => '1',
                'env' => 'sandbox',
                'created_at' => now(),
                'updated_at' => now()
            )
        );

        VTPassApi::insert($mobile_topup);
    }
}
