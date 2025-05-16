<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\TransactionSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'add' => 'Add Money Charge',
            'gift_card' => 'Gift Card Charges',
            'mobile_topup' => 'Mobile Topup Charges',
            'utility_payment' => 'Utility Payment Charges'
        ];
        $create = [];
        foreach ($data as $slug => $item) {
            $create[] = [
                'admin_id'          => 1,
                'slug'              => $slug,
                'title'             => $item,
                'max_limit'         => 50000,
            ];
        }
        TransactionSetting::insert($create);
    }
}
