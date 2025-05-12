<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\BasicSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BasicSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $basic_settings = array(
            array('id' => '1','site_name' => 'PayLoad','site_title' => 'Airtime | Data Bundles | Gift cards and VTU Full Solution','base_color' => '#3782dd','otp_exp_seconds' => '3600','timezone' => 'Asia/Dhaka','user_registration' => '1','secure_password' => '0','agree_policy' => '1','force_ssl' => '1','email_verification' => '1','sms_verification' => '0','email_notification' => '1','push_notification' => '1','kyc_verification' => '1','site_logo_dark' => '72aa9f19-871c-46fd-8a8b-80d5cf789bd2.webp','site_logo' => '337c72c5-31fa-4ba1-98de-69bf9758d45b.webp','site_fav_dark' => '1f5a5653-1098-437f-b185-91fb421a9d3b.webp','site_fav' => '233e4002-af45-4106-b782-8199e28d44ca.webp','preloader_image' => NULL,'mail_config' => '{"method":"smtp","host":"appdevs.team","port":"465","encryption":"ssl","username":"noreply@appdevs.team","password":"QP2fsLk?80Ac","from":"noreply@appdevs.team","app_name":"Payload"}','mail_activity' => NULL,'push_notification_config' => '{"method":"pusher","instance_id":"3488cd8e-e72f-4bb3-9de1-9e8e0511d5b6","primary_key":"2AC53D6674DCB87FC56834DA05C7F900F54DE998E234B94C4B158B5F2E2B427F"}','push_notification_activity' => NULL,'broadcast_config' => '{"method":"pusher","app_id":"1574360","primary_key":"971ccaa6176db78407bf","secret_key":"a30a6f1a61b97eb8225a","cluster":"ap2"}','broadcast_activity' => NULL,'sms_config' => '{"account_sid":"AC1555f39e445ebf50dcaa999216bb5a59","auth_token":"421cf599ec078393b34561ca7c79992f","from":"+19012212041","name":"twilio"}','sms_activity' => NULL,'sms_api' => 'hi {{name}}, {{message}}','web_version' => '1.2.0','admin_version' => '2.5.0','created_at' => '2024-10-25 13:06:19','updated_at' => '2024-12-11 17:36:32')
          );

        BasicSettings::insert($basic_settings);
    }
}
