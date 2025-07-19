<?php

namespace App\Http\Controllers;

use App\Constants\PaymentGatewayConst;
use App\Constants\SiteSectionConst;
use Illuminate\Http\Request;
use App\Models\Admin\ExchangeRate;
use App\Models\Admin\SiteSections;
use App\Models\Admin\UsefulLink;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Jenssegers\Agent\Facades\Agent;

class GlobalController extends Controller
{

    /**
     * Funtion for get state under a country
     * @param country_id
     * @return json $state list
     */
    public function getStates(Request $request)
    {
        $request->validate([
            'country_id' => 'required|integer',
        ]);
        $country_id = $request->country_id;
        // Get All States From Country
        $country_states = get_country_states($country_id);
        return response()->json($country_states, 200);
    }


    public function getCities(Request $request)
    {
        $request->validate([
            'state_id' => 'required|integer',
        ]);

        $state_id = $request->state_id;
        $state_cities = get_state_cities($state_id);

        return response()->json($state_cities, 200);
        // return $state_id;
    }


    public function getCountries(Request $request)
    {
        $countries = get_all_countries();
        return response()->json($countries, 200);
    }


    public function getTimezones(Request $request)
    {
        $timeZones = get_all_timezones();

        return response()->json($timeZones, 200);
    }

    public function receiverWallet(Request $request)
    {
        $currency_code = $request->code;

        if ($request->iso2) {
            $currency_code = get_country_code_by_iso2($request->iso2);
        }

        $receiver_currency = ExchangeRate::where(['currency_code' => $currency_code])->first();

        // if ($request->expectsJson()) {
        //     return response()->json([
        //         'status' => 'success',
        //         'message' => 'wallet returned',
        //         'data' => $receiver_currency
        //     ], 200);
        // }

        return $receiver_currency;
    }

    public function receiverApiWallet(Request $request)
    {
        $currency_code = $request->code;

        if ($request->iso2) {
            $currency_code = get_country_code_by_iso2($request->iso2);
        }

        $receiver_currency = ExchangeRate::where(['currency_code' => $currency_code])->first();

        return response()->json([
            'status' => 'success',
            'message' => 'wallet returned',
            'data' => $receiver_currency
        ], 200);
    }

    //reloadly webhook response
    public function webhookInfo(Request $request)
    {
        $response_data = $request->all();
        $custom_identifier = $response_data['data']['customIdentifier'];
        $transaction = Transaction::where('type', PaymentGatewayConst::MOBILETOPUP)->where('callback_ref', $custom_identifier)->first();
        if ($response_data['data']['status'] == "SUCCESSFUL") {
            $transaction->update([
                'status' => true,
            ]);
        } elseif ($response_data['data']['status'] != "SUCCESSFUL") {
            $afterCharge = (($transaction->creator_wallet->balance + $transaction->details->charges->payable) - $transaction->details->charges->agent_total_commission);
            $transaction->update([
                'status'            => PaymentGatewayConst::STATUSREJECTED,
                'available_balance' =>  $afterCharge,
            ]);
            //refund balance
            $transaction->creator_wallet->update([
                'balance'   => $afterCharge,
            ]);
        }
        logger("Mobile Top Up Success!", ['custom_identifier' => $custom_identifier, 'status' => $response_data['data']['status']]);
    }

    public function userfullPage($slug)
    {
        $page = UsefulLink::where('slug', $slug)->where('status', 1)->first();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        if (empty($page)) {
            abort(404);
        }

        return view('frontend.usefull_pages', compact('page', 'footer'));
    }

    public function setCookie(Request $request)
    {
        $userAgent = $request->header('User-Agent');
        $cookie_status = $request->type;
        if ($cookie_status == 'allow') {
            $response_message = __("Cookie Allowed Success");
            $expirationTime = 2147483647; //Maximum Unix timestamp.
        } else {
            $response_message = __("Cookie Declined");
            $expirationTime = Carbon::now()->addHours(24)->timestamp; // Set the expiration time to 24 hours from now.
        }
        $browser = Agent::browser();
        $platform = Agent::platform();
        $ipAddress = $request->ip();
        return response($response_message)->cookie('approval_status', $cookie_status, $expirationTime)
            ->cookie('user_agent', $userAgent, $expirationTime)
            ->cookie('ip_address', $ipAddress, $expirationTime)
            ->cookie('browser', $browser, $expirationTime)
            ->cookie('platform', $platform, $expirationTime);
    }
}
