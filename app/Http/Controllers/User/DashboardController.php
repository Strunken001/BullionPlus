<?php

namespace App\Http\Controllers\User;

use App\Constants\DataBundleConst;
use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Constants\SiteSectionConst;
use App\Http\Helpers\MobileTopUpHelper;
use App\Http\Helpers\PushNotificationHelper;
use App\Http\Helpers\Response;
use App\Models\Admin\QuickRecharges;
use App\Models\Admin\SiteSections;
use App\Models\Transaction;
use App\Models\UserWallet;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        $page_title     = "Dashboard";
        $user_wallet    = UserWallet::where('user_id', auth()->user()->id)->first();
        $section_slug   = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer         = SiteSections::getData($section_slug)->first();
        $bttn_slug      = 'quick-recharge';
        $quick_bttns    = QuickRecharges::where('key', $bttn_slug)->first();
        $mobile_topup_count = Transaction::where('user_id', auth()->user()->id)->where('type', NotificationConst::MOBILE_TOPUP)->count();
        $giftcard_count = Transaction::where('user_id', auth()->user()->id)->where('type', NotificationConst::GIFTCARD)->count();
        $add_money_count = Transaction::where('user_id', auth()->user()->id)->where('type', NotificationConst::ADD_MONEY)->count();
        $utility_payment_count = Transaction::where('user_id', auth()->user()->id)->where('type', NotificationConst::BILL_PAY)->count();
        $data_bundle_count = Transaction::where('user_id', auth()->user()->id)->where('type', NotificationConst::DATA_BUNDLE)->count();

        $country_code = get_country_by_phone_code(auth()->user()->mobile_code);
        $mobile       = auth()->user()->full_mobile;

        try {
            $get_operators = (new MobileTopUpHelper())->getInstance()->getOperatorsByCountry($country_code);
        } catch (Exception $e) {
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();
            return Response::error([$message], [], 500);
        }

        $operator_id = null;
        foreach ($get_operators as $key => $x) {
            if ($x['bundle']) {
                $operator_id = $x['operatorId'];
            }
        }

        $bundles = [];

        $section_slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $services       = SiteSections::getData($section_slug)->first();

        return view('user.page.dashboard', compact(
            "page_title",
            "footer",
            "user_wallet",
            "quick_bttns",
            "mobile_topup_count",
            "giftcard_count",
            "add_money_count",
            "operator_id",
            "utility_payment_count",
            "data_bundle_count",
            "services"
        ));
    }

    public function rechargeHistory()
    {
        $page_title = "Recharge History";
        $transactions = Transaction::where('user_id', auth()->user()->id)->with(
            'payment_gateway:name',
            'gateway_currency:id,name,alias,payment_gateway_id,currency_code,rate',
        )->AddMoney()->paginate(5);
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        return view('user.page.recharge-history', compact("page_title", "footer", "transactions"));
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::logout();
        return redirect()->route('user.login');
    }
}
