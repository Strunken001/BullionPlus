<?php

namespace Database\Seeders;

use App\Models\Admin\QuickRecharges;
use Illuminate\Database\Seeder;
use Database\Seeders\User\UserSeeder;
use Database\Seeders\Admin\RoleSeeder;
use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\Admin\CurrencySeeder;
use Database\Seeders\Admin\LanguageSeeder;
use Database\Seeders\Admin\SetupKycSeeder;
use Database\Seeders\Admin\SetupSeoSeeder;
use Database\Seeders\Admin\ExtensionSeeder;
use Database\Seeders\Admin\SetupPageSeeder;
use Database\Seeders\Admin\UsefulLinkSeeder;
use Database\Seeders\Admin\AppSettingsSeeder;
use Database\Seeders\Admin\AdminHasRoleSeeder;
use Database\Seeders\Admin\AppOnBoardSeeder;
use Database\Seeders\Admin\SiteSectionsSeeder;
use Database\Seeders\Admin\BasicSettingsSeeder;
use Database\Seeders\FreshSeeder\BasicSettingsSeeder as FreshBasicSettings;
use Database\Seeders\Admin\BlogCategorySeeder;
use Database\Seeders\Admin\BlogSeeder;
use Database\Seeders\Admin\ContactMessagesSeeder;
use Database\Seeders\Admin\ExchangeRateSeeder;
use Database\Seeders\Admin\GiftCardApiSeeder;
use Database\Seeders\Admin\PaymentGatewaySeeder;
use Database\Seeders\Admin\QuickRechargesSeeder;
use Database\Seeders\Admin\ReloadlyTopUpSeeder;
use Database\Seeders\Admin\SmsTemplateSeeder;
use Database\Seeders\Admin\SystemMaintenanceSeeder;
use Database\Seeders\Admin\TransactionSettingSeeder;
use Database\Seeders\Admin\VtpassApiDiscountSeeder;
use Database\Seeders\Admin\VTPassTopUpSeeder;
use Database\Seeders\User\UserKYCSeeder;
use Database\Seeders\User\UserWalletsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Demo
        // $this->call([
        //     AdminSeeder::class,
        //     RoleSeeder::class,
        //     TransactionSettingSeeder::class,
        //     CurrencySeeder::class,
        //     BasicSettingsSeeder::class,
        //     SetupSeoSeeder::class,
        //     AppSettingsSeeder::class,
        //     AppOnBoardSeeder::class,
        //     SiteSectionsSeeder::class,
        //     SetupKycSeeder::class,
        //     ExtensionSeeder::class,
        //     AdminHasRoleSeeder::class,
        //     UserSeeder::class,
        //     SetupPageSeeder::class,
        //     LanguageSeeder::class,
        //     UsefulLinkSeeder::class,
        //     PaymentGatewaySeeder::class,
        //     ExchangeRateSeeder::class,
        //     QuickRechargesSeeder::class,
        //     ContactMessagesSeeder::class,
        //     UserWalletsSeeder::class,
        //     GiftCardApiSeeder::class,
        //     ReloadlyTopUpSeeder::class,
        //     BlogCategorySeeder::class,
        //     SystemMaintenanceSeeder::class,
        //     BlogSeeder::class,
        //     UserKYCSeeder::class,
        //     SmsTemplateSeeder::class,
        // ]);


        // fresh seeder
        $this->call([
            AdminSeeder::class,
            RoleSeeder::class,
            TransactionSettingSeeder::class,
            CurrencySeeder::class,
            FreshBasicSettings::class,
            SetupSeoSeeder::class,
            AppSettingsSeeder::class,
            AppOnBoardSeeder::class,
            SiteSectionsSeeder::class,
            SetupKycSeeder::class,
            ExtensionSeeder::class,
            AdminHasRoleSeeder::class,
            SetupPageSeeder::class,
            LanguageSeeder::class,
            UsefulLinkSeeder::class,
            PaymentGatewaySeeder::class,
            ExchangeRateSeeder::class,
            QuickRechargesSeeder::class,
            GiftCardApiSeeder::class,
            ReloadlyTopUpSeeder::class,
            BlogCategorySeeder::class,
            SystemMaintenanceSeeder::class,
            BlogSeeder::class,
            SmsTemplateSeeder::class,
            VTPassTopUpSeeder::class,
            VtpassApiDiscountSeeder::class,
        ]);
    }
}
