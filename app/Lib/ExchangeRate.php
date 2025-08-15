<?php

namespace App\Lib;

use Error;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class ExchangeRate
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env("EXCHANGE_RATES_BASE_URL");
        $this->apiKey = env("EXCHANGE_RATES_API_KEY");
    }

    public function getUSDExchangeRates()
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . '/latest?access_key=' . $this->apiKey . '&base=USD')
            ->throw(function (Response $response, RequestException $exception) {
                $message = $exception->getMessage();
                throw new Exception($message);
            })->json();

        if (!$response['success']) {
            Log::error('Unsuccessful request');
            throw new Error('Unsuccessful request');
        }

        return $response['rates'];
    }
}
