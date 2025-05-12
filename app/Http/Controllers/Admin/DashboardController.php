<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Providers\Admin\BasicSettingsProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Pusher\PushNotifications\PushNotifications;
use App\Models\Admin\AdminNotification;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Helpers\PushNotificationHelper;
use App\Http\Helpers\Response;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Models\User;
use App\Models\UserSupportTicket;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __("Dashboard");

        $last_month_start = Carbon::now()->subMonth()->startOfMonth()->startOfDay();
        $last_month_end = Carbon::now()->subMonth()->endOfMonth()->endOfDay();
        $this_month_start = Carbon::now()->startOfMonth()->startOfDay();
        $this_month_end = Carbon::now()->endOfDay();

        $this_week = Carbon::now()->subWeek()->toDateString();
        $this_month = Carbon::now()->subMonth()->toDateString();
        $this_year = Carbon::now()->subYear()->toDateString();
        $transactions = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)->latest()->take(5)->get();


        // Recharge Money
        $recharge_money_total_balance = Transaction::toBase()->where('type', PaymentGatewayConst::TYPEADDMONEY)->sum('request_amount');
        $completed_recharge_money =  Transaction::toBase()
            ->where('type', PaymentGatewayConst::TYPEADDMONEY)
            ->whereBetween('created_at', [$this_month_start, $this_month_end])
            ->where('status', 1)
            ->sum('request_amount');
        $pending_recharge_money =  Transaction::toBase()
            ->where('status', 2)
            ->where('type', PaymentGatewayConst::TYPEADDMONEY)
            ->whereBetween('created_at', [$this_month_start, $this_month_end])
            ->sum('request_amount');

        if ($pending_recharge_money == 0) {
            $recharge_money_percent = 0;
        } else {
            $recharge_money_percent = ($completed_recharge_money / ($completed_recharge_money + $pending_recharge_money)) * 100;
        }

        // gift card
        $total_gift_card = Transaction::toBase()->where('type', PaymentGatewayConst::GIFTCARD)->sum('request_amount');
        $gift_card_this_month =  Transaction::toBase()
            ->where('type', PaymentGatewayConst::GIFTCARD)
            ->whereBetween('created_at', [$this_month_start, $this_month_end])
            ->where('status', 1)
            ->sum('request_amount');
        $gift_card_last_month =  Transaction::toBase()
            ->where('status', 2)
            ->where('type', PaymentGatewayConst::GIFTCARD)
            ->whereBetween('created_at', [$last_month_start, $last_month_end])
            ->sum('request_amount');

        if ($gift_card_this_month == 0) {
            $gift_card_percent = 0;
        } else {
            $gift_card_percent = ($gift_card_this_month / ($gift_card_this_month + $gift_card_last_month)) * 100;
        }

        // mobile topup
        $total_mobile_topup = Transaction::toBase()->where('type', PaymentGatewayConst::MOBILETOPUP)->sum('request_amount');
        $mobile_topup_this_month =  Transaction::toBase()
            ->where('type', PaymentGatewayConst::MOBILETOPUP)
            ->whereBetween('created_at', [$this_month_start, $this_month_end])
            ->where('status', 1)
            ->sum('request_amount');
        $mobile_topup_last_month =  Transaction::toBase()
            ->where('status', 2)
            ->whereBetween('created_at', [$last_month_start, $last_month_end])
            ->where('type', PaymentGatewayConst::MOBILETOPUP)
            ->sum('request_amount');

        if ($mobile_topup_this_month == 0) {
            $mobile_topup_percent = 0;
        } else {
            $mobile_topup_percent = ($mobile_topup_this_month / ($mobile_topup_this_month + $mobile_topup_last_month)) * 100;
        }

        //total profits
        $total_profits = TransactionCharge::toBase()->sum('total_charge');

        $this_month_profits = TransactionCharge::toBase()
            ->whereBetween('created_at', [$this_month_start, $this_month_end])
            ->sum('total_charge');

        $last_month_profits = TransactionCharge::toBase()
            ->whereBetween('created_at', [$last_month_start, $last_month_end])
            ->sum('total_charge');

        if ($this_month_profits == 0) {
            $profit_percent  = 0;
        } else {
            $profit_percent = ($this_month_profits / ($this_month_profits + $last_month_profits)) * 100;
        }

        //Users
        $total_users = User::toBase()->count();

        $verified_users =  User::MobileVerified()->count();
        $unverified_users =User::MobileUnverified()->count();

        if($unverified_users == 0 && $verified_users != 0){
            $user_percent = 100;
        }elseif($unverified_users == 0 && $verified_users == 0){
         $user_percent = 0;
        }else{
           $user_percent = ($verified_users / ($verified_users + $unverified_users)) * 100;
        }

        //Support tickets
        $total_tickets = UserSupportTicket::toBase()->count();

        $active_tickets =  UserSupportTicket::active()->count();
        $pending_tickets = UserSupportTicket::Pending()->count();

        if($pending_tickets == 0 && $active_tickets != 0){
            $ticket_percent = 100;
        }elseif($pending_tickets == 0 && $active_tickets == 0){
         $ticket_percent = 0;
        }else{
           $ticket_percent = ($active_tickets / ($active_tickets + $pending_tickets)) * 100;
        }


        //charts
        // Monthly Add Money
        $start = Carbon::now()->startOfMonth();;
        $end   = Carbon::now()->endOfMonth();;



        // Add Money
        $pending_data  = [];
        $success_data  = [];
        $canceled_data = [];
        $hold_data     = [];
        // Giftcard
        $Gift_pending_data  = [];
        $Gift_success_data  = [];
        $Gift_canceled_data = [];
        $Gift_hold_data     = [];
        //Mobile topup
        $mobile_topup_pending_data  = [];
        $mobile_topup_success_data  = [];
        $mobile_topup_canceled_data = [];
        $mobile_topup_hold_data     = [];



        $month_day  = [];
        while ($start->lessThanOrEqualTo($end)) {
            $start_date = $start->format('Y-m-d');

            // Monthly add money
            $pending = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->whereDate('created_at', $start_date)
                ->where('status', 2)
                ->count();
            $success = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->whereDate('created_at', $start_date)
                ->where('status', 1)
                ->count();
            $canceled = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->whereDate('created_at', $start_date)
                ->where('status', 4)
                ->count();
            $hold = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->whereDate('created_at', $start_date)
                ->where('status', 3)
                ->count();
            $pending_data[]  = $pending;
            $success_data[]  = $success;
            $canceled_data[] = $canceled;
            $hold_data[]     = $hold;

            // Monthly Giftcard
            $giftcard_pending = Transaction::where('type', PaymentGatewayConst::GIFTCARD)
                ->whereDate('created_at', $start_date)
                ->where('status', 2)
                ->count();
            $giftcard_success = Transaction::where('type', PaymentGatewayConst::GIFTCARD)
                ->whereDate('created_at', $start_date)
                ->where('status', 1)
                ->count();
            $giftcard_canceled = Transaction::where('type', PaymentGatewayConst::GIFTCARD)
                ->whereDate('created_at', $start_date)
                ->where('status', 4)
                ->count();
            $giftcard_hold = Transaction::where('type', PaymentGatewayConst::GIFTCARD)
                ->whereDate('created_at', $start_date)
                ->where('status', 3)
                ->count();
            $giftcard_pending_data[]  = $giftcard_pending;
            $giftcard_success_data[]  = $giftcard_success;
            $giftcard_canceled_data[] = $giftcard_canceled;
            $giftcard_hold_data[]     = $giftcard_hold;

            //Monthley mobile topup
            $topup_pending = Transaction::where('type', PaymentGatewayConst::MOBILETOPUP)
                ->whereDate('created_at', $start_date)
                ->where('status', 2)
                ->count();
            $topup_success = Transaction::where('type', PaymentGatewayConst::MOBILETOPUP)
                ->whereDate('created_at', $start_date)
                ->where('status', 1)
                ->count();
            $topup_canceled = Transaction::where('type', PaymentGatewayConst::MOBILETOPUP)
                ->whereDate('created_at', $start_date)
                ->where('status', 4)
                ->count();
            $topup_hold = Transaction::where('type', PaymentGatewayConst::MOBILETOPUP)
                ->whereDate('created_at', $start_date)
                ->where('status', 3)
                ->count();

            $mobile_topup_pending_data[]  = $topup_pending;
            $mobile_topup_success_data[]  = $topup_success;
            $mobile_topup_canceled_data[] = $topup_canceled;
            $mobile_topup_hold_data[]    = $topup_hold;

            $month_day[] = $start->format('Y-m-d');
            $start->addDay();
        }

        $total_user = User::toBase()->count();
        $unverified_user = User::toBase()->where('sms_verified', 0)->count();
        $active_user = User::toBase()->where('status', 1)->count();
        $banned_user = User::toBase()->where('status', 0)->count();
        // Chart four | User analysis
        $chart_four = [$active_user, $banned_user, $unverified_user, $total_user];


        // Chart one
        $chart_one_data = [
            'pending_data'  => $pending_data,
            'success_data'  => $success_data,
            'canceled_data' => $canceled_data,
            'hold_data'     => $hold_data,
        ];
        // Chart two
        $chart_two_data = [
            'pending_data'  => $giftcard_pending_data,
            'success_data'  => $giftcard_success_data,
            'canceled_data' => $giftcard_canceled_data,
            'hold_data'     => $giftcard_hold_data,
        ];
        // Chart three
        $chart_three_data = [
            'pending_data'  => $mobile_topup_pending_data,
            'success_data'  => $mobile_topup_success_data,
            'canceled_data' => $mobile_topup_canceled_data,
            'hold_data'     => $mobile_topup_hold_data,
        ];


        $data = [
            'recharge_money_total_balance'    => $recharge_money_total_balance,
            'completed_recharge_money'        => $completed_recharge_money,
            'pending_recharge_money'          => $pending_recharge_money,
            'recharge_money_percent'          => $recharge_money_percent,

            'total_gift_card'                 => $total_gift_card,
            'gift_card_this_month'            => $gift_card_this_month,
            'gift_card_last_month'            => $gift_card_last_month,
            'gift_card_percent'               => $gift_card_percent,

            'total_mobile_topup'              => $total_mobile_topup,
            'mobile_topup_this_month'         => $mobile_topup_this_month,
            'mobile_topup_last_month'         => $mobile_topup_last_month,
            'mobile_topup_percent'            => $mobile_topup_percent,

            'total_profits'                   => $total_profits,
            'this_month_profits'              => $this_month_profits,
            'last_month_profits'              => $last_month_profits,
            'profit_percent'                  => $profit_percent,

            'total_users'           => $total_users,
            'verified_users'        => $verified_users,
            'unverified_users'      => $unverified_users,
            'user_percent'          => $user_percent,

            'total_tickets'         => $total_tickets,
            'active_tickets'        => $active_tickets,
            'pending_tickets'       => $pending_tickets,
            'ticket_percent'        => $ticket_percent,

            'chart_one_data'        => $chart_one_data,
            'chart_two_data'        => $chart_two_data,
            'chart_three_data'      => $chart_three_data,
            'chart_four_data'       => $chart_four,
            'month_day'             => $month_day,
            'transactions'          => $transactions
        ];

        return view('admin.sections.dashboard.index', compact(
            'page_title',
            'data'
        ));
    }

    /**
     * Logout Admin From Dashboard
     * @return view
     */
    public function logout(Request $request)
    {

        $admin = auth()->user();
        pusher_unsubscribe("admin", $admin->id);

        try {
            $admin->update([
                'last_logged_out'   => now(),
                'login_status'      => false,
            ]);
        } catch (Exception $e) {
            // Handle Error
        }

        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Function for clear admin notification
     */
    public function notificationsClear()
    {
        $admin = auth()->user();

        if (!$admin) {
            return false;
        }

        try {
            $admin->update([
                'notification_clear_at'     => now(),
            ]);
        } catch (Exception $e) {
            $error = ['error' => [__('Something went wrong! Please try again.')]];
            return Response::error($error, null, 404);
        }

        $success = ['success' => [__('Notifications clear successfully!')]];
        return Response::success($success, null, 200);
    }
}
