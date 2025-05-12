<?php

namespace Database\Seeders\Admin;

use App\Models\Frontend\ContactRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contact_requests = array(
            array('id' => '1','name' => 'Dale Henry','email' => 'dobequnyke@mailinator.com','phone' => NULL,'message' => 'Qui rerum non esse a','reply' => '0','created_at' => '2024-09-11 06:41:23','updated_at' => '2024-09-11 06:41:23'),
            array('id' => '2','name' => 'Tahmid Khan Sajid','email' => 'tahmidkhansajid.official@gmail.com','phone' => NULL,'message' => 'hello','reply' => '1','created_at' => '2024-09-11 06:44:19','updated_at' => '2024-09-11 06:44:50')
        );

        ContactRequest::insert($contact_requests);
    }
}
