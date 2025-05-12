<?php

namespace Database\Seeders\User;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = array(
            array('id' => '7', 'firstname' => 'test', 'lastname' => 'user1', 'username' => 'zeuswilkins', 'email' => 'user@appdevs.net', 'mobile_code' => '93', 'mobile' => '123456789', 'full_mobile' => '93123456789', 'password' => '$2y$10$G1WjKXe3I4bKqy3yOvAPReNJzGk9ET.9SiPfF6HoFjBpNFN2X6SsW', 'referral_id' => NULL, 'image' => '046944e3-55a6-4183-b02c-07750167786a.webp', 'status' => '1', 'address' => '{"country":"Bangladesh","state":"","city":"","zip":"","address":""}', 'email_verified' => '0', 'sms_verified' => '0', 'kyc_verified' => '1', 'ver_code' => NULL, 'ver_code_send_at' => NULL, 'two_factor_verified' => '0', 'two_factor_status' => '0', 'two_factor_secret' => 'BB2PC7QTA7NQQZXL', 'email_verified_at' => NULL, 'remember_token' => '6lKQ9wkPEDJssOo3Yo6WXppbYmNIwdCkbCMDbuXO8ZoxIRbXWY3xJKdF9xh7', 'deleted_at' => NULL, 'created_at' => '2024-09-02 03:37:28', 'updated_at' => '2024-11-18 10:46:37'),
            array('id' => '8', 'firstname' => 'test', 'lastname' => 'user2', 'username' => 'testuser2', 'email' => 'user2@appdevs.net', 'mobile_code' => '880', 'mobile' => '9874563210', 'full_mobile' => '8809874563210', 'password' => '$2y$10$rT72zmA6XRWdFo0Po2aNiupGVU0DXQcMLTOsvOsrYmdVDK4qd5mre', 'referral_id' => NULL, 'image' => NULL, 'status' => '1', 'address' => '{"country":"Bangladesh"}', 'email_verified' => '0', 'sms_verified' => '1', 'kyc_verified' => '0', 'ver_code' => NULL, 'ver_code_send_at' => NULL, 'two_factor_verified' => '0', 'two_factor_status' => '0', 'two_factor_secret' => NULL, 'email_verified_at' => NULL, 'remember_token' => NULL, 'deleted_at' => NULL, 'created_at' => '2024-11-18 10:38:16', 'updated_at' => '2024-11-18 10:38:16'),
            array('id' => '9', 'firstname' => 'test', 'lastname' => 'user3', 'username' => 'testuser3', 'email' => 'user3@appdevs.net', 'mobile_code' => '880', 'mobile' => '112233445566778899', 'full_mobile' => '880112233445566778899', 'password' => '$2y$10$j8awBFj9o243b3RvsrzM6e2aO1b5epdo1F7iPT0wmiiwEG8hk4ruu', 'referral_id' => NULL, 'image' => NULL, 'status' => '1', 'address' => '{"country":"Bangladesh"}', 'email_verified' => '0', 'sms_verified' => '1', 'kyc_verified' => '0', 'ver_code' => NULL, 'ver_code_send_at' => NULL, 'two_factor_verified' => '0', 'two_factor_status' => '0', 'two_factor_secret' => NULL, 'email_verified_at' => NULL, 'remember_token' => NULL, 'deleted_at' => NULL, 'created_at' => '2024-11-18 10:40:05', 'updated_at' => '2024-11-18 10:40:05')
        );

        User::insert($users);
    }
}
