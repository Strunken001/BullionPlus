<?php

namespace Database\Seeders\User;

use App\Models\UserKycData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserKYCSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_kyc_data = array(
            array(
                'id' => '1',
                'user_id' => '7',
                'data' => '[{"type":"select","label":"ID Type","name":"id_type","required":true,"validation":{"max":0,"min":0,"mimes":[],"options":["NID"," Driving License"," Passport"],"required":true},"value":"Driving License"},{"type":"file","label":"Front","name":"front","required":true,"validation":{"max":"2","mimes":["jpg"," png"],"min":0,"options":[],"required":true},"value":"b16baa4a-d52a-4ff2-9de1-a97d3b9a725c.webp"},{"type":"file","label":"Back","name":"back","required":false,"validation":{"max":"2","mimes":["jpg"," png"],"min":0,"options":[],"required":false},"value":"43b8ed80-9557-44b0-95ec-69cfa0d577da.webp"}]',
                'reject_reason' => 'no',
                'created_at' => '2024-09-27 12:28:13',
                'updated_at' => '2024-09-27 12:28:33'
            )
        );

        UserKycData::insert($user_kyc_data);
    }
}
