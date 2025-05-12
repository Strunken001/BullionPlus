<?php

namespace App\Http\Helpers;

use Exception;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\MobileTopUp;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Constants\GlobalConst;
use App\Events\User\MobileTopUpEvent;
use App\Http\Helpers\MobileTopUpHelper;
use App\Models\Admin\Currency;
use App\Models\Admin\ExchangeRate;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Models\Admin\TransactionSetting;
use App\Models\Admin\MobileTopUpProvider;
use App\Models\Admin\ReloadlyApi;
use App\Models\Transaction;
use App\Traits\Transaction\MobileTopUp as TransactionMobileTopUp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Client\RequestException;
use Illuminate\Validation\ValidationException;

class Reloadly {

    // use TransactionMobileTopUp;

    /**
     * Provider slug
     */
    const SLUG = "RELOADLY";

    /**
     * store gateway credentials
     */
    public object $credentials;

    /**
     * store user
     */
    public \App\Models\User|null $user = null;

    /**
     * access token cache key
     */
    const API_ACCESS_TOKEN = "MOBILE-TOPUP-RELOADLY-API-ACCESS-TOKEN";

    /**
     * countries cache key
     */
    const COUNTRIES_CACHE_KEY = "MOBILE-TOPUP-RELOADLY-COUNTRIES";

    /**
     * store topup types
     */
    const TOPUP_RECHARGE    = "RECHARGE";
    const TOPUP_BUNDLE      = "BUNDLE";
    const TOPUP_DATA        = "DATA";

    /**
     * exchange status
     */
    public array $exchange_status = [
        // 'PROCESSING'    => GlobalConst::STRING_PROCESSING,
        // 'SUCCESSFUL'    => GlobalConst::STRING_SUCCESS,
        // 'FAILED'        => GlobalConst::STRING_FAILED,
        // 'REFUNDED'      => GlobalConst::STRING_REFUNDED,
    ];

    /**
     * store access token
     */
    public $access_token;

    /**
     * store number
     */
    public string $phone_number;

    /**
     * country iso 2
     */
    public string $country_iso2;

    /**
     * set topup type
     */
    public string $topup_type;

    /**
     * charge result
     */
    public array $charge_result = [
        'amount'                => null, // it wil convert to local amount
        'request_amount'        => null, // this is the input amount from frontend - sometimes it comes with merchant amount
        'merchant_currency_code'=> null,
        'merchant_currency_rate'=> null,
        'merchant_exchange_rate'=> null,
        'wallet_currency_code'  => null,
        'receiver_currency_code'=> null,
        'default_currency_code' => null,
        'exchange_rate'         => null,
        'fixed_charge'          => null,
        'percent_charge'        => null,
        'fixed_charge_calc'     => null,
        'percent_charge_calc'   => null,
        'total_charge_calc'     => null,
        'wallet_balance'        => null,
        'charge_exchange_rate'  => null,
        'charge_currency'       => null,
        'total_payable'         => null,
        'min_limit'             => null,
        'max_limit'             => null,
        'daily_limit'           => null,
        'monthly_limit'         => null,
        'min_limit_calc'        => null,
        'max_limit_calc'        => null,
        'daily_limit_calc'      => null,
        'monthly_limit_calc'    => null,
        'operator_has_limit'    => null,
        'operator_min_limit'    => null,
        'operator_max_limit'    => null,
    ];

    /**
     * constructor
     * @param \App\Models\Admin\ReloadlyApi $provider
     */
    public function __construct(public ReloadlyApi $provider)
    {
        // set credentials
        $this->setCredentials($provider);
        $this->accessToken();
    }

    /**
     * set gateway credentials
     * @param \App\Models\Admin\ReloadlyApi $provider
     * @return object
     */
    public function setCredentials(ReloadlyApi $provider):object
    {
        $provider_credentials = $provider->credentials;

        $credential_collect = $provider_credentials;

        $credentials = [
            'client_id'                 => $credential_collect->client_id,
            'secret_key'                => $credential_collect->secret_key,
            // 'client_webhook_secret'     => $credential_collect->where('name', 'client_secret')->first()?->value,
            'prod_url'                  => $credential_collect->production_base_url,
            'sand_url'                  => $credential_collect->sandbox_base_url,
            'env'                       => $provider->env,
        ];

        $req_url = $provider->env == "sandbox" ? $credentials['sand_url'] : $credentials['prod_url'];
        $credentials['req_url']   =  $req_url;

        $this->credentials = (object) $credentials;

        return $this->credentials;
    }

    /**
     * Authenticate API access token retrieve
     */
    public function accessToken():array
    {
        // if(cache()->driver("file")->get(self::API_ACCESS_TOKEN)) {

        //     $access_token = cache()->driver("file")->get(self::API_ACCESS_TOKEN);
        //     $this->access_token = $access_token;

        //     return [
        //         'token'         => cache()->driver("file")->get(self::API_ACCESS_TOKEN),
        //         'expire_in'     => 0,
        //     ];
        // }

        if(!$this->credentials) $this->setCredentials($this->provider);

        $request_endpoint = "https://auth.reloadly.com/oauth/token";

        $client_id = $this->credentials->client_id;
        $secret_key = $this->credentials->secret_key;
        $request_url    = $this->credentials->req_url;

        $grant_type = "client_credentials";

        // dd($this->credentials);

        $response = Http::post($request_endpoint,[
            "client_id" => $client_id,
            "client_secret" => $secret_key,
            "grant_type" => $grant_type,
            "audience" => $request_url,
        ])->throw(function(Response $response, RequestException $exception) {
            $response = $response->json();

            $message = $response['message'];
            $message_type   = $response['errorCode'];

            $error_message = $message . " [$message_type]";

            throw new Exception($error_message);
        })->json();

        $access_token   = $response['access_token'];
        $expire_in      = $response['expires_in'];

        // set cache driver
        // cache()->driver("file")->put(self::API_ACCESS_TOKEN, $access_token, ($expire_in / 2));

        // set access token
        $this->access_token = $access_token;

        return [
            'token'         => $access_token,
            'expire_in'     => $expire_in,
        ];
    }

    /**
     * return index view file
     * @param string $page_title
     * @return View
     */
    public function indexView(string $page_title)
    {
        $provider       = $this->provider->only(['id']);
        $user           = $this->user ?? auth()->user();

        $currency_codes = Currency::active()->pluck("code")->toArray();

        $supported_countries = collect($this->getCountries())->whereIn('currencyCode', $currency_codes)
                                                            ->pluck("currencyCode", "isoName")
                                                            ->toArray();

        $user_country = $this->user->country_code ?? "";

        return view('payment-gateway.mobile-topup.reloadly.index', compact('provider','page_title','supported_countries','user_country','user'));
    }

    /**
     * get all available countries from API
     * @return array
     */
    public function getCountries():array
    {

        if(cache()->driver("file")->get(self::COUNTRIES_CACHE_KEY))
            return cache()->driver("file")->get(self::COUNTRIES_CACHE_KEY);

        $provider = $this->provider;
        $credentials = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/countries";
        $access_token = $this->access_token;

        $response = Http::withHeaders([
            'Authorization' => "Bearer " . $access_token,
            'accept'        => "application/com.reloadly.topups-v1+json",
        ])->get($endpoint)->throw(function(Response $response, RequestException $exception) {
            $message = $exception->getMessage();
            throw new Exception($message);
        })->json();

        // set cache
        cache()->driver('file')->put(self::COUNTRIES_CACHE_KEY, $response, 1800);

        return $response;
    }

    /**
     * set user
     * @param \App\Models\User $user
     * @return self
     */
    public function user(User $user):self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * set phone number
     * @param string $phone_number
     * @return self
     */
    public function setPhone(string $phone_number):self
    {
        $this->phone_number = $phone_number;
        return $this;
    }

    /**
     * set country code
     * @param string $country_iso2
     * @return self
     */
    public function setCountry(string $country_iso2):self
    {
        $this->country_iso2 = strtoupper($country_iso2);
        return $this;
    }

    /**
     * set topUp type
     * @param string $topup_type
     * @return self
     */
    public function setTopUpType(string $topup_type):self
    {
        $this->topup_type = $topup_type;
        return $this;
    }

    /**
     * get mobile money charges
     * @param array $data
     * @return array
     */
    public function getCharges(array $data):array
    {
        $validated          = $this->validateChargeData($data);
        $operator           = $this->getOperator($validated['operator'], $validated['cache_key'] ?? null);

        $local_currency     = $operator['destinationCurrencyCode'];
        $rate               = $operator['fx']['rate'];

        $merchant_currency  = $operator['senderCurrencyCode'];
        $denominationType   = $operator['denominationType'];
        $isGeoPlan          = $operator['supportsGeographicalRechargePlans'];

        $request_amount     = $amount = $validated['amount'] ?? 0; // $amount is local amount
        $isAmountLocal      = true;

        if($isGeoPlan && !isset($validated['geo_location'])) {
            throw ValidationException::withMessages([
                'operator'      => "Geo location is required for this operator!",
            ]);
        }


        // need to convert amount in local currency
        if($denominationType == "FIXED" && $isGeoPlan) {
            // if local amount is not available then the incoming amount
            $geo_location = collect($operator['geographicalRechargePlans'])->where('locationCode', $validated['geo_location'])->first();

            if(!$geo_location) {
                throw ValidationException::withMessages(['geo_location' => ['Invalid Geo Location Selected!']]);
            }

            $local_amounts = $geo_location['localAmounts'] ?? [];

            if(count($local_amounts) == 0) $isAmountLocal = false;

        }else {
            // check fixed amount is local or foreign
            $local_amounts = $operator['localFixedAmounts'] ?? [];

            if(count($local_amounts) == 0) $isAmountLocal = false;

        }

        if(!$isAmountLocal) {
            $amount = $amount * $rate; // convert amount to local amount
        }

        $user_wallet        = auth()->user()->wallets;// GBP
        $wallet_currency    = $user_wallet->currency;

        $receiver_currency  = ExchangeRate::where('currency_code', $local_currency)->first(); // XOF
        $merchant_currency  = ExchangeRate::where('currency_code', $operator['senderCurrencyCode'])->first(); // BDT
        $default_currency   = Currency::default(); // USD

        $merchant_pay_amount        = $amount / $rate;
        $default_currency_amount    = $merchant_pay_amount / $merchant_currency->rate; // USD

        $exchange_wallet_amount     = $default_currency_amount * $wallet_currency->rate; // GBP

        // exchange rate
        $receiver_currency_exchange_rate    = 1 / $rate; // 1 XOF = ? BDT
        $default_currency_exchange_amount   = $receiver_currency_exchange_rate / $merchant_currency->rate; // 1 XOF = ? USD

        $exchange_rate                      = $default_currency_exchange_amount * $wallet_currency->rate; // 1 USD = ? GBP, that means 1 XOF = ? GBP (wallet currency)

        $transaction_charges    = TransactionSetting::where('slug','mobile_topup')->first();

        $fixed_charge           = $transaction_charges->fixed_charge;
        $fixed_charge_calc      = $fixed_charge * $wallet_currency->rate;

        $percent_charge         = $transaction_charges->percent_charge;
        $percent_charge_calc    = (($amount / 100) * $percent_charge) * $exchange_rate;

        $total_charge_calc      = $fixed_charge_calc + $percent_charge_calc;
        $total_payable          = $exchange_wallet_amount + $total_charge_calc;

        $min_limit              = $transaction_charges->min_limit;
        $max_limit              = $transaction_charges->max_limit;
        $daily_limit            = $transaction_charges->daily_limit;
        $monthly_limit          = $transaction_charges->monthly_limit;

        $min_limit_calc         = $min_limit * $wallet_currency->rate;
        $max_limit_calc         = $max_limit * $wallet_currency->rate;

        $operator_has_limit     = false;
        $local_min_amount       = $local_max_amount = 0;



        $this->charge_result = [
            'amount'                        => get_amount($amount), // local amount
            'request_amount'                => $request_amount,
            'merchant_currency_code'        => $merchant_currency->code, // merchant currency
            'merchant_currency_rate'        => $merchant_currency->rate, // merchant currency
            'merchant_exchange_rate'        => get_amount($receiver_currency_exchange_rate),
            'wallet_currency_code'          => $wallet_currency->code,
            'wallet_currency_rate'          => $wallet_currency->rate,
            'receiver_currency_code'        => $receiver_currency->code,
            'default_currency_code'         => $default_currency->code,
            'exchange_rate'                 => get_amount($exchange_rate, null, 4),
            'fixed_charge'                  => get_amount($fixed_charge),
            'percent_charge'                => get_amount($percent_charge, null, 2),
            'fixed_charge_calc'             => get_amount($fixed_charge_calc),
            'percent_charge_calc'           => get_amount($percent_charge_calc),
            'total_charge_calc'             => get_amount($total_charge_calc),
            'wallet_balance'                => get_amount($user_wallet->balance),
            'charge_exchange_rate'          => get_amount($wallet_currency->rate),
            'charge_currency'               => $wallet_currency->code,
            'exchange_amount'               => get_amount($exchange_wallet_amount),
            'total_payable'                 => get_amount($total_payable),
            'min_limit'                     => get_amount($min_limit),
            'max_limit'                     => get_amount($max_limit),
            'min_limit_calc'                => get_amount($min_limit_calc),
            'max_limit_calc'                => get_amount($max_limit_calc),
            'operator_has_limit'            => $operator_has_limit,
            'operator_min_limit'            => get_amount($local_min_amount),
            'operator_max_limit'            => get_amount($local_max_amount),
            'operator_name'                 => $operator['name'] ?? "--",
            'operator'                      => $operator ?? "--",
            'bundle_currency'               => $local_currency ?? "--",
        ];

        return $this->charge_result;
    }

    /**
     * validate charge data
     * @param array $data
     */
    public function validateChargeData(array $data)
    {
        $validator = Validator::make($data, [
            'operator'              => 'required|string',
            'amount'                => 'nullable|numeric|gte:0',
            'cache_key'             => 'nullable|string',
            'geo_location'          => 'nullable|string', // is required for geo recharge plan supported operator
        ]);

        if($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->all());
        }

        $validated = $validator->validate();

        return $validated;
    }

    /**
     * detect operator from phone number
     * @param string $phone_number
     * @param string $country_iso2
     * @return array
     */
    public function autoDetectOperator(string $phone_number, string $country_iso2):array
    {
        $provider       = $this->provider;
        $credentials    = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/operators/auto-detect/phone/$phone_number/countries/$country_iso2";
        $access_token = $this->access_token;

        $response = Http::withHeaders([
            'Authorization' => "Bearer " . $access_token,
            'accept'        => "application/com.reloadly.topups-v1+json",
        ])->get($endpoint)->throw(function(Response $response, RequestException $exception) {
            $message = $exception->getMessage();
            throw new Exception($message);
        })->json();

        return $response;
    }

    /**
     * get all operators
     * @param array $filters
     */
    public function getOperators(array $filters = [])
    {
        $provider       = $this->provider;
        $credentials    = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/operators";
        $access_token = $this->access_token;

        $response = Http::withHeaders([
            'Authorization' => "Bearer " . $access_token,
            'accept'        => "application/com.reloadly.topups-v1+json",
        ])->get($endpoint, $filters)->throw(function(Response $response, RequestException $exception) {
            $message = $exception->getMessage();
            throw new Exception($message);
        })->json();

        return $response;
    }

    /**
     * get operators by country
     * @param string $country_iso2
     */
    public function getOperatorsByCountry(string $country_iso2, $filters = [])
    {
        $provider       = $this->provider;
        $credentials    = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/operators/countries/$country_iso2";
        $access_token = $this->access_token;

        $response = Http::timeout(180)->withHeaders([
            'Authorization' => "Bearer " . $access_token,
            'accept'        => "application/com.reloadly.topups-v1+json",
        ])->get($endpoint, $filters)->throw(function(Response $response, RequestException $exception) {
            $message = $response->json()['message'] ?? $exception->getMessage();
            throw new Exception($message);
        })->json();

        return $response;
    }

    /**
     * get single operator information
     * @param string $operator_id
     * @param string|null $cache_key
     * @return array $operator
     */
    public function getOperator(string $operator_id, string $cache_key = null):array
    {
        if($cache_key && cache()->driver("file")->get($cache_key)) {
            $operator = collect(cache()->driver("file")->get($cache_key))->where('id', $operator_id)->first();

            return $operator;
        }

        // hit api and get operator
        $provider       = $this->provider;
        $credentials    = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/operators/" . $operator_id;
        $access_token = $this->access_token;

        $response = Http::withHeaders([
            'Authorization' => "Bearer " . $access_token,
            'accept'        => "application/com.reloadly.topups-v1+json",
        ])->get($endpoint)->throw(function(Response $response, RequestException $exception) {
            $message = $exception->getMessage();
            throw new Exception($message);
        })->json();

        return $response;
    }

    /**
     * validate submit data
     * @param array $data
     */
    public function validateSubmitData(array $data)
    {
        $provider               = $this->provider;
        $transaction_charges    = $this->getCharges($data);
        $operator               = $this->getOperator($data['operator'], $validated['cache_key'] ?? null);

        $isGeoPlan          = $operator['supportsGeographicalRechargePlans'];
        if($isGeoPlan) {
            $geo_location = collect($operator['geographicalRechargePlans'])->where('locationCode', $data['geo_location'])->first();

            $operator['geographicalRechargePlans'] = [$geo_location];
        }

        $data['transaction_charges']    = $transaction_charges;
        $data['operator']               = $operator;
        $data['mobile_topup_provider_model']    = $provider;

        // check operator limit
        if($transaction_charges['operator_has_limit'] == true) {
            if($transaction_charges['amount'] < $transaction_charges['operator_min_limit'] || $transaction_charges['amount'] > $transaction_charges['operator_max_limit']) {
                throw new Exception("Please follow the recharge limit!");
            }
        }

        // check transaction limit
        if($transaction_charges['total_payable'] < $transaction_charges['min_limit_calc'] || $transaction_charges['total_payable'] > $transaction_charges['max_limit_calc']) {
            throw new Exception("Please follow the transaction limit!");
        }

        return $data;
    }

    /**
     * return preview view file
     * @param \App\Models\TemporaryData
     * @param string $page_title
     */
    public function previewView(TemporaryData $temp_data, string $page_title)
    {
        $provider = $this->provider;

        return view('payment-gateway.mobile-topup.reloadly.preview', compact('page_title','provider','temp_data'));
    }

    /**
     * mobile top up request send
     * @param \App\Models\MobileTopUp $mobile_topup
     */
    public function topUp($request)
    {
        $operator = $request->operator ?? false;
        if(!$operator) throw new Exception("Oops! Operator not found or invalid!");

        $extra_data         = $request;
        $denomination_type  = $operator['denominationType'];
        $isGeoPlanSupport   = $operator['supportsGeographicalRechargePlans'];
        $useLocalAmount     = true;

        if($denomination_type == "FIXED" || $isGeoPlanSupport) {

            if($isGeoPlanSupport){
                $geo_location = $extra_data->geo_location ?? "";
                $geo_plan = collect($operator['geographicalRechargePlans'] ?? [])->where('locationCode', $geo_location)->first();

                $local_amounts = $geo_plan['localAmounts'] ?? [];
            }else {
                $local_amounts = $operator['localFixedAmounts'] ?? [];
            }

            if(count($local_amounts) == 0) {
                $useLocalAmount     = false;
            }

        }

        $api_topup = $this->sendTopUpRequest([
            'amount'            => $request->request_amount,
            'customIdentifier'  => $request->trx_ref,
            'operatorId'        => $operator['id'],
            'recipientPhone'    => [
                'countryCode'   => $request->recharge_country_iso2,
                'number'        => $request->phone,
            ],
            'useLocalAmount'    => $useLocalAmount
        ]);

        $api_status  = $api_topup['status'];

        return [
            'status'    => $this->exchange_status[$api_status] ?? "",
            'response'  => $api_topup,
        ];
    }

    /**
     * send a mobile topup request to the api
     * @param $request_data
     */
    public function sendTopUpRequest(array $request_data)
    {
        $provider       = $this->provider;
        $credentials    = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/topups";
        $access_token = $this->access_token;

        $response = Http::withHeaders([
            'Authorization' => "Bearer " . $access_token,
            'accept'        => "application/com.reloadly.topups-v1+json",
        ])->post($endpoint, $request_data)->throw(function(Response $response, RequestException $exception) {
            $message = $response->json()['message'] ?? $exception->getMessage();
            throw new Exception($message);
        })->json();

        return $response;
    }

    /**
     * Receive webhook response
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\MobileTopUp
     */
    public function receiveWebhook(Request $request)
    {
        $signing_secret = $this->credentials['client_webhook_secret'];

        $request_signature  = $request->header('X-Reloadly-Signature');
        $request_timestamp  = $request->header('X-Reloadly-Request-Timestamp');
        $payload            = $request->getContent();

        $data_to_sign = $payload . ":" . $request_timestamp;

        try {
            $computed_signature = hash_hmac('sha256', $data_to_sign, $signing_secret, false);
        } catch (Exception $e) {
            // Invalid payload
            return response("Invalid Payload Or Webhook Secret", 400);
        }

        if($request_signature != $computed_signature) {
            return response("Invalid Signature", 400);
        }

        $response_data = $request->all();

        if($response_data['type'] == "airtime_transaction.status") {

            $api_status = $response_data['data']['status'] ?? $response_data['status'];
            $status     = $this->exchange_status[$api_status];

            $trx_ref = $response_data['data']['customIdentifier'];

            $transaction = Transaction::where('trx_ref', $trx_ref)->first();

            $update_gift_card = $this->updateOrderStatus($transaction, [
                'status'    => $status,
                'response'  => $response_data,
            ], MobileTopUpHelper::RESPONSE_WEBHOOK);

            return $update_gift_card;
        }
    }



}
