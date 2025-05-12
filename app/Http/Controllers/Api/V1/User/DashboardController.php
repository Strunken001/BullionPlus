<?php

namespace App\Http\Controllers\Api\V1\User;

use Carbon\CarbonPeriod;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Constants\NotificationConst;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;
use App\Constants\SiteSectionConst;
use App\Models\Admin\PaymentGateway;
use App\Models\Admin\QuickRecharges;
use App\Models\Admin\SiteSections;
use App\Models\UserHasInvestPlan;
use App\Providers\Admin\CurrencyProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $default_currency = CurrencyProvider::default();

        // User Wallets
        $user_wallets = UserWallet::auth()->whereHas('currency', function ($q) use ($default_currency) {
            $q->where('code', $default_currency->code);
        })->select('id', 'user_id', 'currency_id', 'balance', 'status', 'created_at')->with(['currency' => function ($q) {
            $q->select('id', 'code');
        }])->get();


        $lang = request()->lang;
        $default = 'en';

        // Banner
        $banners = SiteSections::where('key', Str::slug(SiteSectionConst::BANNER_SECTION))->first()->value->items;


        $ban_data = [];

        foreach ($banners as $key => $value) {
            $ban_data[] = [
                'image' => $value->image,
            ];
        };

        $gateway = PaymentGateway::take(6)->get();
        $gateway->makeHidden([
            "slug",
            "code",
            "alias",
            "credentials",
            "supported_currencies",
            "desc",
            "input_fields",
            "env",
            "status",
            "last_edit_by",
            "created_at",
            "updated_at",
            "currencies",
        ]);

        $bttns =  QuickRecharges::take(3)->first();
        $recharge_bttn = [];

        $index = 0;

        foreach ($bttns->buttons->items as $key => $value) {
            $recharge_bttn[$index] = $value->amount;
            $index = $index + 1;
        };

        // User Information
        $user_info = auth()->user()->only([
            'id',
            'firstname',
            'lastname',
            'fullname',
            'username',
            'email',
            'image',
            'mobile_code',
            'mobile',
            'full_mobile',
            'email_verified',
            'kyc_verified',
            'two_factor_verified',
            'two_factor_status',
            'two_factor_secret',
        ]);

        $mobile_topup_count = Transaction::where('user_id',auth()->user()->id)->where('type',NotificationConst::MOBILE_TOPUP)->count();
        $giftcard_count = Transaction::where('user_id',auth()->user()->id)->where('type',NotificationConst::GIFTCARD)->count();
        $add_money_count = Transaction::where('user_id',auth()->user()->id)->where('type',NotificationConst::ADD_MONEY)->count();

        $profile_image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("user-profile"),
            'default_image'     => files_asset_path_basename("profile-default"),
        ];

        $gateway_image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("payment-gateways"),
        ];

        $banner_image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("site-section"),
        ];

        return Response::success([__('User dashboard data fetch successfully!')], [
            'instructions'          => [],
            'user_info'             => $user_info,
            'wallets'               => $user_wallets,
            'banner'                => $ban_data,
            'payment_gateway'       => $gateway,
            'recharge_bttn'         => $recharge_bttn,
            'profile_image_paths'   => $profile_image_paths,
            'gateway_image_paths'   => $gateway_image_paths,
            'banner_image_paths'    => $banner_image_paths,
            'mobile_topup_count'    => $mobile_topup_count,
            'giftcard_count'        => $giftcard_count,
            'add_money_count'       => $add_money_count,
        ]);
    }

    public function notifications()
    {
        return Response::warning([__('This section is under maintenance!')], [], 503);
    }
}
