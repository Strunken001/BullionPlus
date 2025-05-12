<?php

namespace Database\Seeders\Admin;

use App\Models\Frontend\BlogsCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $blogs_categories = array(
            array('id' => '3','admin_id' => '1','name' => '{"language":{"en":{"name":"News"},"fr":{"name":"Nouvelles"},"es":{"name":"Noticias"},"ar":{"name":"\\u0623\\u062e\\u0628\\u0627\\u0631"}}}','status' => '1','created_at' => '2024-09-04 04:22:22','updated_at' => '2024-12-11 15:20:42'),
            array('id' => '4','admin_id' => '1','name' => '{"language":{"en":{"name":"Offer"},"fr":{"name":"Offre"},"es":{"name":"Oferta"},"ar":{"name":"\\u0639\\u0631\\u0636"}}}','status' => '1','created_at' => '2024-09-16 04:40:20','updated_at' => '2024-12-11 16:23:05'),
            array('id' => '5','admin_id' => '1','name' => '{"language":{"en":{"name":"Tech"},"fr":{"name":"Technologie"},"es":{"name":"tecnolog\\u00eda"},"ar":{"name":"\\u0627\\u0644\\u062a\\u0643\\u0646\\u0648\\u0644\\u0648\\u062c\\u064a\\u0627"}}}','status' => '1','created_at' => '2024-09-16 04:42:56','updated_at' => '2024-12-11 15:18:56')
        );

        BlogsCategory::insert($blogs_categories);
    }
}
