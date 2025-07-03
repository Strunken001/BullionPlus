<?php

namespace App\Libs;

use Error;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class YouVerify
{
    public function __construct() {}

    public function kycVerification(array $data)
    {
        try {
            switch ($data['document']) {
                case 'nin':
                    $response = $this->verifyNIN([
                        'id' => $data['id'],
                        'image' => $data['image']
                    ]);

                    return $response['validations']['selfie']['selfieVerification']['match'];

                case 'drivers_license':
                    $response = $this->verifyLicense([
                        'id' => $data['id'],
                        'image' => $data['image']
                    ]);

                    return $response['validations']['selfie']['selfieVerification']['match'];

                case 'passport':
                    $response = $this->verifyLicense([
                        'id' => $data['id'],
                        'image' => $data['image'],
                        'lastName' => $data['lastName']
                    ]);

                    return $response['validations']['selfie']['selfieVerification']['match'];

                default:
                    throw new Error('Not a valid document type');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Error("An error occured performing KYC verification");
        }
    }

    public function verifyNIN(array $data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => env('YOUVERIFY_API_KEY'),
        ])->post(env('YOUVERIFY_BASE_URL') . '/v2/api/identity/ng/nin', [
            'id' => $data['id'],
            'isSubjectConsent' => true,
            'validations' => [
                'selfie' => [
                    'image' => $data['image'],
                ]
            ],
            'premiumNin' => true
        ])
            ->throw(function (Response $response, RequestException $exception) {
                $message = !empty($response->json()['data']['validations']['validationMessages'])
                    ? $response->json()['data']['validations']['validationMessages']
                    : (!empty($response->json()['message'])
                        ? $response->json()['message']
                        : $exception->getMessage());

                throw new Exception($message);
            })->json();

        if (!$response['success']) {
            Log::error('Unsuccessful request');
            throw new Error('Unsuccessful request');
        }

        return $response['data'];
    }

    public function verifyLicense(array $data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => env('YOUVERIFY_API_KEY'),
        ])->post(env('YOUVERIFY_BASE_URL') . '/v2/api/identity/ng/drivers-license', [
            'id' => $data['id'],
            'isSubjectConsent' => true,
            'validations' => [
                'selfie' => [
                    'image' => $data['image'],
                ]
            ]
        ])
            ->throw(function (Response $response, RequestException $exception) {
                $message = !empty($response->json()['data']['validations']['validationMessages'])
                    ? $response->json()['data']['validations']['validationMessages']
                    : (!empty($response->json()['message'])
                        ? $response->json()['message']
                        : $exception->getMessage());

                throw new Exception($message);
            })->json();

        if (!$response['success']) {
            Log::error('Unsuccessful request');
            throw new Error('Unsuccessful request');
        }

        return $response['data'];
    }

    public function verifyPassport(array $data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => env('YOUVERIFY_API_KEY'),
        ])->post(env('YOUVERIFY_BASE_URL') . '/v2/api/identity/ng/passport', [
            'id' => $data['id'],
            'isSubjectConsent' => true,
            'lastName' => $data['lastName'],
            'validations' => [
                'selfie' => [
                    'image' => $data['image'],
                ]
            ]
        ])
            ->throw(function (Response $response, RequestException $exception) {
                $message = !empty($response->json()['data']['validations']['validationMessages'])
                    ? $response->json()['data']['validations']['validationMessages']
                    : (!empty($response->json()['message'])
                        ? $response->json()['message']
                        : $exception->getMessage());

                throw new Exception($message);
            })->json();

        if (!$response['success']) {
            Log::error('Unsuccessful request');
            throw new Error('Unsuccessful request');
        }

        return $response['data'];
    }
}
