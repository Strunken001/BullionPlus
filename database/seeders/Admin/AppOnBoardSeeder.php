<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use App\Models\Admin\AppOnboardScreens;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AppOnBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $app_onboard_screens = array(
            array('id' => '1','title' => NULL,'sub_title' => NULL,'image' => '1fde2de5-5ba1-4a2b-8810-df1d27e48ce9.webp','status' => '1','last_edit_by' => '1','created_at' => '2024-11-15 18:40:18','updated_at' => '2024-11-15 18:40:18')
        );
        AppOnboardScreens::insert($app_onboard_screens);
    }
}
