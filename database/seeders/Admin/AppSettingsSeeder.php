<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\AppSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $app_settings = array(
            array('id' => '1','version' => '1.2.0','splash_screen_image' => 'dc42f1fe-27d3-4801-a44a-6d591a28dc18.webp','url_title' => NULL,'android_url' => NULL,'iso_url' => NULL,'created_at' => '2024-11-15 18:37:21','updated_at' => '2024-11-15 18:40:03')
        );

        AppSettings::upsert($app_settings,['id'],['android_url','iso_url']);
    }
}
