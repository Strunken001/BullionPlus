<?php

namespace App\Http\Helpers;

use App\Models\Admin\Currency;
use App\Models\Admin\ExchangeRate;
use App\Models\Admin\TransactionSetting;
use App\Models\VTPassApi;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class VTPass
{
    public object $credentials;
    public $provider;
    public string $access_token;
    public $charge_result;

    public function __construct()
    {
        $this->provider = VTPassApi::mobileTopUp()->first();
        $this->setCredentials($this->provider);
    }

    public function setCredentials(VTPassApi $provider): object
    {
        $provider_credentials = $provider->credentials;

        $credential_collect = $provider_credentials;

        $credentials = [
            'api_key'                   => $credential_collect->api_key,
            'secret_key'                => $credential_collect->secret_key,
            'public_key'                => $credential_collect->public_key,
            'prod_url'                  => $credential_collect->production_base_url,
            'sand_url'                  => $credential_collect->sandbox_base_url,
            'env'                       => $provider->env,
        ];

        $req_url = $provider->env == "sandbox" ? $credentials['sand_url'] : $credentials['prod_url'];
        $credentials['req_url']   =  $req_url;

        $this->credentials = (object) $credentials;

        return $this->credentials;
    }

    public function mobileTopUp(array $params)
    {
        $credentials = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/api/pay";

        $response = Http::withHeaders([
            'api-key' => $this->credentials->api_key,
            'secret-key' => $this->credentials->secret_key
        ])->post($endpoint, [
            'request_id' => now()->getTimestamp(),
            'serviceID' => $params['service_id'],
            'amount' => $params['amount'],
            'phone' => $params['phone']
        ])->throw(function (Response $response, RequestException $exception) {
            $message = $exception->getMessage();
            throw new Exception($message);
        })->json();

        $response['content']['transactions']['message'] = $response['response_description'];
        $response['content']['transactions']['status'] = $response['content']['transactions']['status'] === "delivered" ? "SUCCESSFUL" : strtoupper($response['content']['transactions']['status']);
        $response['content']['transactions']['customIdentifier'] = $params['customIdentifier'];

        return $response['content']['transactions'];
    }

    public function getDataBundleVariationCodes(array $params)
    {
        $credentials = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/api/service-variations?serviceID=" . $params['service_id'];

        $response = Http::withHeaders([
            'api-key' => $this->credentials->api_key,
            'public-key' => $this->credentials->public_key
        ])->get($endpoint)->throw(function (Response $response, RequestException $exception) {
            $message = $exception->getMessage();
            throw new Exception($message);
        })->json();

        return $response['content']['variations'];
    }

    public function dataBundleTopUp(array $params)
    {
        $credentials = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/api/pay";

        $response = Http::withHeaders([
            'api-key' => $this->credentials->api_key,
            'secret-key' => $this->credentials->secret_key
        ])->post($endpoint, [
            'request_id' => now()->getTimestamp(),
            'serviceID' => $params['service_id'],
            'variation_code' => $params['variation_code'],
            'phone' => $params['phone'],
            'billersCode' => $params['phone'],
            'amount' => $params['amount'],
        ])->throw(function (Response $response, RequestException $exception) {
            $message = $exception->getMessage();
            throw new Exception($message);
        })->json();

        $response['content']['response'] = $response['content']['transactions'];
        $response['content']['response']['recipientPhone'] = $response['content']['transactions']['unique_element'];
        $response['content']['status'] = $response['content']['transactions']['status'] === "delivered" ? "SUCCESSFUL" : strtoupper($response['content']['transactions']['status']);
        $response['content']['message'] = $response['response_description'];
        $response['content']['requestId'] = $response['requestId'];
        $response['content']['response']['customIdentifier'] = Str::uuid() . "|" . "DATABUNDLE";

        return $response['content'];
    }

    public function utilityPayment(array $params)
    {
        $credentials = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/api/pay";

        $response = Http::withHeaders([
            'api-key' => $this->credentials->api_key,
            'secret-key' => $this->credentials->secret_key
        ])->post($endpoint, [
            'request_id' => now()->getTimestamp(),
            'serviceID' => $params['service_id'],
            'billersCode' => $params['account_number'],
            'variation_code' => $params['variation_code'], // prepaid or postpaid
            'amount' => $params['amount'],
            'phone' => $params['phone'],
        ])->throw(function (Response $response, RequestException $exception) {
            $message = $exception->getMessage();
            Log::info(['message' => $message]);
            throw new Exception($message);
        })->json();

        Log::info(['utilityPayment response' => $response]);

        return $response;
    }

    public function verifyUtilityPaymentTransaction(string $request_id)
    {
        $credentials = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/api/requery";

        $response = Http::withHeaders([
            'api-key' => $this->credentials->api_key,
            'secret-key' => $this->credentials->secret_key
        ])->post($endpoint, [
            'request_id' => $request_id,
        ])->throw(function (Response $response, RequestException $exception) {
            $message = $exception->getMessage();
            throw new Exception($message);
        })->json();

        $response['content']['transaction'] = $response['content']['transactions'];
        $response['content']['transaction']['status'] = $response['content']['transactions']['status'] === "delivered" ? "SUCCESSFUL" : strtoupper($response['content']['transactions']['status']);
        $response['content']['transaction']['billDetails']['type'] = $response['content']['transactions']['type'];
        $response['content']['transaction']['billDetails']['pinDetails']['token'] = explode(":", $response['purchased_code'])[1] ?? "--";

        return $response['content'];
    }

    public function getCharges(array $data): array
    {
        $local_currency     = "NGN";
        $merchant_currency  = "NGN";

        $request_amount     = $amount = $data['amount'] ?? 0; // $amount is local amount

        $user_wallet        = auth()->user()->wallets; // GBP
        $wallet_currency    = $user_wallet->currency;

        $receiver_currency  = ExchangeRate::where('currency_code', $local_currency)->first();
        $merchant_currency  = ExchangeRate::where('currency_code', $merchant_currency)->first();
        $default_currency   = Currency::default(); // USD

        $merchant_pay_amount        = $request_amount;
        $default_currency_amount    = $merchant_pay_amount / $merchant_currency->rate;

        $exchange_wallet_amount     = $default_currency_amount * $default_currency->rate;

        // exchange rate
        $receiver_currency_exchange_rate    = $receiver_currency->rate;
        $default_currency_exchange_amount   = $merchant_pay_amount / $receiver_currency->rate;

        $exchange_rate                      = 1 / $merchant_currency->rate;

        $transaction_charges    = TransactionSetting::where('slug', 'mobile_topup')->first();

        $fixed_charge           = $transaction_charges->fixed_charge;
        $fixed_charge_calc      = $fixed_charge * $wallet_currency->rate;

        $percent_charge         = $transaction_charges->percent_charge;
        $percent_charge_calc    = (($request_amount / 100) * $percent_charge) * $exchange_rate;

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
            'operator_name'                 => $data['operator'] ?? "--",
            'operator'                      => $operator ?? "--",
            'bundle_currency'               => $local_currency ?? "--",
        ];

        return $this->charge_result;
    }

    public function verifyMeterNumber(array $params)
    {
        $credentials = $this->credentials;

        $base_url = $credentials->req_url;
        $endpoint = rtrim($base_url, "/") . "/api/merchant-verify";

        $response = Http::withHeaders([
            'api-key' => $this->credentials->api_key,
            'secret-key' => $this->credentials->secret_key
        ])->post($endpoint, [
            'serviceID' => $params['service_id'],
            'billersCode' => $params['account_number'],
            'type' => $params['variation_code'], // prepaid or postpaid
        ])->throw(function (Response $response, RequestException $exception) {
            $message = $exception->getMessage();
            throw new Exception($message);
        })->json();

        return $response;
    }
}
