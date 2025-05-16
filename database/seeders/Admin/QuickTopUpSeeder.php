<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\QuickRecharges;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuickTopUpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quick_recharges = array(
            array(
                'id' => '1',
                'key' => 'quick-topup',
                'buttons' => '{"items":{"66f2a3e9b61bd":{"id":"66f2a3e9b61bd","amount":"20","status":1},"66f2b44ba0d29":{"id":"66f2b44ba0d29","amount":"30","status":1},"66f387a5c4554":{"id":"66f387a5c4554","amount":"40","status":1},"66f387a9c8664":{"id":"66f387a9c8664","amount":"50","status":1}}}',
                'created_at' => '2024-09-24 11:09:36',
                'updated_at' => '2024-09-25 03:46:49'
            )
        );

        QuickRecharges::insert($quick_recharges);
    }
}
