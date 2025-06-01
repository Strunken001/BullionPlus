<?php

namespace Database\Seeders\Admin;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VtpassApiDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $data = [
            ['Aba Electric Payment - ABEDC', 1.7, 'utility_bill', 'aba-electric'],
            ['Abuja Electricity Distribution Company- AEDC', 1.2, 'utility_bill', 'abuja-electric'],
            ['Airtel Airtime VTU', 3.4, 'airtime', 'airtel'],
            ['Airtel Data', 3.4, 'data_bundle', 'airtel-data'],
            ['Benin Electricity - BEDC', 1.5, 'utility_bill', 'benin-electric'],
            ['Eko Electric Payment - EKEDC', 1.0, 'utility_bill', 'eko-electric'],
            ['Enugu Electric - EEDC', 1.4, 'utility_bill', 'enugu-electric'],
            ['9mobile Airtime VTU', 4.0, 'airtime', 'etisalat'],
            ['9mobile Data', 4.0, 'data_bundle', 'etisalat-data'],
            ['GLO Airtime VTU', 4.0, 'airtime', 'glo'],
            ['GLO Data', 4.0, 'data_bundle', 'glo-data'],
            ['GLO Data (SME)', 4.0, 'data_bundle', 'glo-sme-data'],
            ['IBEDC - Ibadan Electricity Distribution Company', 1.1, 'utility_bill', 'ibadan-electric'],
            ['Ikeja Electric Payment - IKEDC', 0.2, 'utility_bill', 'ikeja-electric'],
            ['Jos Electric - JED', 0.9, 'utility_bill', 'jos-electric'],
            ['Kaduna Electric - KAEDCO', 1.5, 'utility_bill', 'kaduna-electric'],
            ['KEDCO - Kano Electric', 1.0, 'utility_bill', 'kano-electric'],
            ['MTN Airtime VTU', 3.0, 'airtime', 'mtn'],
            ['MTN Data', 3.0, 'data_bundle', 'mtn-data'],
            ['PHED - Port Harcourt Electric', 0.4, 'utility_bill', 'portharcourt-electric'],
            ['Yola Electric Disco Payment - YEDC', 1.2, 'utility_bill', 'yola-electric'],
        ];

        foreach ($data as [$service, $discount, $type, $serviceId]) {
            DB::table('vtpass_api_discounts')->insert([
                'service' => $service,
                'api_discount_percentage' => $discount,
                'type' => $type,
                'service_id' => $serviceId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
