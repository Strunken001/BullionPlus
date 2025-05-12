<?php

namespace Database\Seeders\User;

use App\Models\UserWallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserWalletsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_wallets = array(
            array('id' => '2', 'user_id' => '7', 'currency_id' => '1', 'balance' => '1000.00000000', 'status' => '1', 'created_at' => '2024-09-02 03:37:28', 'updated_at' => '2024-11-15 12:26:36'),
            array('id' => '3', 'user_id' => '8', 'currency_id' => '1', 'balance' => '0.00000000', 'status' => '1', 'created_at' => '2024-11-18 10:38:16', 'updated_at' => NULL),
            array('id' => '4', 'user_id' => '9', 'currency_id' => '1', 'balance' => '0.00000000', 'status' => '1', 'created_at' => '2024-11-18 10:40:05', 'updated_at' => NULL)
        );

        UserWallet::insert($user_wallets);
    }
}
