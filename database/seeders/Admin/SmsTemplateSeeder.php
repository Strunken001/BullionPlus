<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\SmsTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmsTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sms_templates = array(
            array('act' => 'LOGIN_VERIFY','name' => 'Login verify','subj' => 'Login Verify','sms_body' => 'Your account account verify: {{code}}','sms_status' => '1','created_at' => '2019-09-25 05:04:05','updated_at' => '2021-01-06 06:49:06'),
            array('act' => 'PASS_RESET_CODE','name' => 'Password Reset','subj' => 'Password Reset','sms_body' => 'Your account recovery code is: {{code}}','sms_status' => '1','created_at' => '2019-09-25 05:04:05','updated_at' => '2021-01-06 06:49:06'),
            array('act' => 'RECHARGE_INVOICE','name' => 'Recharge Invoice','subj' => 'Recharge Invoice','sms_body' => 'Your recharge invoice: TRX-ID: {{trx_id}}  Recharge Amount: {{request_amount}} {{request_currency}}, Payable Amount: {{payable_amount}} {{payable_currency}}','sms_status' => '1','created_at' => '2019-09-25 05:04:05','updated_at' => '2021-01-06 06:49:06'),
            array('act' => 'MONEY_RECHARGE_REJECT','name' => 'Manual Recharge Money- Admin Rejected','subj' => 'Your Money Recharge Request is Rejected','sms_body' => 'Admin Rejected Your {{amount}} {{request_currency}} money recharge request by {{gateway_name}},Rejection Reason: {{rejection_message}},TrxId: {{trx}}','sms_status' => '1','created_at' => '2020-06-10 00:00:00','updated_at' => '2020-06-15 00:00:00'),
            array('act' => 'MOBILE_TOPUP','name' => 'Mobile Top Up Request','subj' => 'Mobile Top Up Successful','sms_body' => 'Mobile TopUp: {{amount}} {{request_currency}}, Mobile Number: {{mobile_number}} successful.TrxID {{trx}}','sms_status' => '1','created_at' => '2023-07-05 14:21:50','updated_at' => '2023-07-05 14:21:50'),
            array('act' => 'GIFT_CARD','name' => 'Gift Card','subj' => 'Gift Card Buying Successful','sms_body' => 'Gift Card: {{amount}} {{request_currency}}, buying successful.TrxID {{trx}}.','sms_status' => '1','created_at' => '2023-07-05 14:21:50','updated_at' => '2023-07-05 14:21:50'),
            array('act' => 'KYC_APPROVED','name' => 'KYC Approved','subj' => 'KYC Approved Successfully','sms_body' => 'Your KYC verification request is approved by admin. Approved At {{time}}.','sms_status' => '1','created_at' => '2023-07-05 17:00:28','updated_at' => '2023-07-05 17:00:28'),
            array('act' => 'KYC_REJECTED','name' => 'KYC Rejected','subj' => 'KYC Rejected','sms_body' => 'Your KYC verification request is rejected by admin. Rejection Reason {{reason}}, Rejected At {{time}}.','sms_status' => '1','created_at' => '2023-07-05 17:02:39','updated_at' => '2023-07-05 17:02:39'),
        );

        SmsTemplate::insert($sms_templates);
    }
}

