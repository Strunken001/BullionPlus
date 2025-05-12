<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\ReloadlyApi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GiftCardApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gift_card_apis = array(
            array('provider' => 'RELOADLY','type' => 'GIFT-CARD','credentials' => '{"client_id":"0Tac1xfwuxvloESuakKYpD34fYCJtqVB","secret_key":"p16MBCEYpy-9KJaYLzAhe7Ux80YmXx-GDXSiXjz9JjBU9EBC8hprAjDNTcHjSp7","production_base_url":"https:\\/\\/giftcards.reloadly.com","sandbox_base_url":"https:\\/\\/giftcards-sandbox.reloadly.com"}','status' => '1','env' => 'sandbox','created_at' =>now(),'updated_at' =>now())
          );

        ReloadlyApi::insert($gift_card_apis);
    }
}
