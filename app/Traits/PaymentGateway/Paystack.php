<?php

namespace App\Traits\PaymentGateway;

use Exception;
use Illuminate\Support\Str;
use App\Models\TemporaryData;
use App\Http\Helpers\PaymentGateway;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Constants\PaymentGatewayConst;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

trait Paystack
{
    private $paystack_gateway_credentials;
    private $request_credentials;
    private $paystack_api_base_url = "https://api.paystack.co";

    public function paystackInit($output = null)
    {
        if (!$output) $output = $this->output;
        $request_credentials = $this->getPaystackRequestCredentials($output);

        return $this->createPaystackPaymentLink($output, $request_credentials);
    }

    public function registerPaystackEndpoints($endpoint_key = null)
    {
        $endpoints = [
            'create-payment-link' => $this->paystack_api_base_url . "/transaction/initialize",
        ];

        if ($endpoint_key) {
            if (!array_key_exists($endpoint_key, $endpoints)) throw new Exception("Endpoint key [$endpoint_key] not registered! Register it in registerPaystackEndpoints() method");

            return $endpoints[$endpoint_key];
        }

        return $endpoints;
    }

    public function createPaystackPaymentLink($output, $request_credentials)
    {
        $endpoint = $this->registerPaystackEndpoints('create-payment-link');

        $temp_record_token = generate_unique_string('temporary_datas', 'identifier', 60);
        $this->setUrlParams("token=" . $temp_record_token); // set Parameter to URL for identifying when return success/cancel

        $redirection = $this->getRedirection();
        $url_parameter = $this->getUrlParams();

        $user = auth()->guard(get_auth_guard())->user();

        $temp_data = $this->paystackJunkInsert($temp_record_token); // create temporary information

        $paymentGatewayInstance = (new PaymentGatewayConst());

        $web_return_url = $paymentGatewayInstance->registerRedirection()['web']['return_url'] ?? 'user.recharge.payment.success';
        $web_cancel_url = $paymentGatewayInstance->registerRedirection()['web']['cancel_url'] ?? 'user.recharge.payment.cancel';
        $api_return_url = $paymentGatewayInstance->registerRedirection()['api']['return_url'] ?? 'user.recharge.payment.success';
        $api_cancel_url = $paymentGatewayInstance->registerRedirection()['api']['cancel_url'] ?? 'user.recharge.payment.cancel';

        // $callback_url = env('APP_URL') . "/user/dashboard";
        // $cancel_url = env('APP_URL') . '/user/recharge/recharge/view';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $request_credentials->token,
            'Content-Type'  => 'application/json',
        ])->post($endpoint, [
            "email"         => $user->email,
            "amount"        => get_amount($output['amount']->total_amount, null, 2) * 100, // as per paystack policy,
            "currency"      => $output['currency']->currency_code,
            // "callback_url"  => $this->setGatewayRoute($redirection['return_url'], PaymentGatewayConst::PAYSTACK, $url_parameter),
            "callback_url"  => route(request()->expectsJson() ? $api_return_url : $web_return_url, PaymentGatewayConst::PAYSTACK),
            "reference"     => $temp_record_token,
            "metadata"      => [
                "cancel_action" => route(request()->expectsJson() ? $api_cancel_url : $web_cancel_url, PaymentGatewayConst::PAYSTACK)
            ]
        ])->throw(function (Response $response, RequestException $exception) use ($temp_data) {
            $temp_data->delete();
            throw new Exception($exception->getMessage());
        })->json();

        $response_array = json_decode(json_encode($response), true);

        $temp_data_contains = json_decode(json_encode($temp_data->data), true);
        $temp_data_contains['response'] = $response_array;

        $temp_data->update([
            'data'  => $temp_data_contains,
        ]);

        $redirect_url = $response_array['data']['authorization_url'] ?? null;
        if (!$redirect_url) throw new Exception("Something went wrong! Please try again");

        if (request()->expectsJson()) {
            $this->output['redirection_response']   = $response_array;
            $this->output['redirect_links']         = [];
            $this->output['redirect_url']           = $redirect_url;
            $this->output['callback_url']           = route($web_return_url, PaymentGatewayConst::PAYSTACK);
            $this->output['cancel_url']             = route($web_cancel_url, PaymentGatewayConst::PAYSTACK);
            return $this->get();
        }

        return redirect()->away($redirect_url);
    }

    public function paystackJunkInsert($temp_token)
    {
        $output = $this->output;

        $data = [
            'gateway'       => $output['gateway']->id,
            'currency'      => $output['currency']->id,
            'amount'        => json_decode(json_encode($output['amount']), true),
            'wallet_table'  => $output['wallet']->getTable(),
            'wallet_id'     => $output['wallet']->id,
            'creator_table' => auth()->guard(get_auth_guard())->user()->getTable(),
            'creator_id'    => auth()->guard(get_auth_guard())->user()->id,
            'creator_guard' => get_auth_guard(),
        ];

        return TemporaryData::create([
            'type'          => PaymentGatewayConst::TYPEADDMONEY,
            'identifier'    => $temp_token,
            'data'          => $data,
            'invoice'       => $output['invoice']
        ]);
    }

    public function getPaystackRequestCredentials($output)
    {
        if (!$this->paystack_gateway_credentials) $this->getPaystackCredentials($output);

        $credentials = $this->paystack_gateway_credentials;
        if (!$output) $output = $this->output;

        $request_credentials = [];
        $request_credentials['token']   = $credentials->secret_key;

        $this->request_credentials = (object) $request_credentials;
        return (object) $request_credentials;
    }

    public function getPaystackApiBaseUrl()
    {
        return $this->paystack_api_base_url;
    }

    public function isPaystack($gateway)
    {
        $search_keyword = ['paystack', 'Paystack', 'paystack gateway'];
        $gateway_name = $gateway->name;

        $search_text = Str::lower($gateway_name);
        $search_text = preg_replace("/[^A-Za-z0-9]/", "", $search_text);
        foreach ($search_keyword as $keyword) {
            $keyword = Str::lower($keyword);
            $keyword = preg_replace("/[^A-Za-z0-9]/", "", $keyword);
            if ($keyword == $search_text) {
                return true;
                break;
            }
        }
        return false;
    }

    public function paystackSuccess($output)
    {
        $redirect_response = $output['tempData']['data']->callback_data ?? false;
        if ($redirect_response == false) {
            throw new Exception("Invalid response");
        }

        $response = $output['tempData']['data']->response;

        // if (!$response->status) {

        //     $identifier = $output['tempData']['identifier'];
        //     $response_array = json_decode(json_encode($redirect_response), true);

        //     if ($output['type'] == PaymentGatewayConst::TYPEADDMONEY) {
        //         return (new AddMoneyController())->cancel(new Request([
        //             'token' => $identifier,
        //         ]), PaymentGatewayConst::PAYSTACK);
        //     }

        //     $this->setUrlParams("token=" . $identifier); // set Parameter to URL for identifying when return success/cancel
        //     $redirection = $this->getRedirection();
        //     $url_parameter = $this->getUrlParams();

        //     $cancel_link = $this->setGatewayRoute($redirection['cancel_url'], PaymentGatewayConst::PAYSTACK, $url_parameter);
        //     return redirect()->away($cancel_link);
        // }

        if ($response) {
            $output['capture'] = $output['tempData']['data']->response ?? "";

            try {
                $this->createTransaction($output);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    public function getPaystackCredentials($output = null)
    {
        $gateway = $output['gateway'] ?? null;
        if (!$gateway) throw new Exception("Payment gateway not available");

        $public_key_sample = ['public key'];
        $secret_key_sample = ['secret key'];
        $encryption_key_sample    = ['encryption', 'encryption key', 'paystack encryption', 'paystack encryption key'];

        $public_key    = PaymentGateway::getValueFromGatewayCredentials($gateway, $public_key_sample);
        $secret_key         = PaymentGateway::getValueFromGatewayCredentials($gateway, $secret_key_sample);

        $mode = $gateway->env;
        $gateway_register_mode = [
            PaymentGatewayConst::ENV_SANDBOX => PaymentGatewayConst::ENV_SANDBOX,
            PaymentGatewayConst::ENV_PRODUCTION => PaymentGatewayConst::ENV_PRODUCTION,
        ];

        if (array_key_exists($mode, $gateway_register_mode)) {
            $mode = $gateway_register_mode[$mode];
        } else {
            $mode = PaymentGatewayConst::ENV_SANDBOX;
        }

        $credentials = (object) [
            'public_key'                => $public_key,
            'secret_key'                => $secret_key,
            'mode'                      => $mode
        ];

        $this->paystack_gateway_credentials = $credentials;

        return $credentials;
    }
}
