<?php

namespace App\Lib;

use Error;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class YouVerify
{
    public function getVerificationUrl(string $country)
    {
        switch ($country) {
            case "Nigeria":
                return [
                    "nin" => [
                        'url' => "/v2/api/identity/ng/nin",
                        'function' => [$this, 'verifyNIN']
                    ],
                    "license" => [
                        'url' => "/v2/api/identity/ng/drivers-license",
                        'function' => [$this, 'verifyLicense']
                    ],
                    "passport" => [
                        'url' => "/v2/api/identity/ng/passport",
                        'function' => [$this, 'verifyPassport']
                    ],
                ];
            case "Ghana":
                return [
                    "nin" => [
                        'url' => "/v2/api/identity/gh/ssnit",
                        'function' => [$this, 'verifyNIN']
                    ],
                    "license" => [
                        "url" => "",
                        'function' => [$this, 'verifyLicense']
                    ],
                    "passport" => [
                        'url' => "/v2/api/identity/gh/passport",
                        'function' => [$this, 'verifyPassport']
                    ]
                ];
            case "Kenya":
                return [
                    "nin" => [
                        'url' => "/v2/api/identity/ke/id-scrub",
                        'function' => [$this, 'verifyKenyanIdentityNumber']
                    ],
                    "license" => [
                        'url' => "/v2/api/identity/ke/drivers-license",
                        'function' => [$this, 'verifyKenyanLicense']
                    ],
                    "passport" => [
                        'url' => "/v2/api/identity/ke/passport",
                        'function' => [$this, 'verifyPassport']
                    ]
                ];
            case "South Africa":
                return [
                    "nin" => [
                        'url' => "/v2/api/identity/za/said",
                        'function' => [$this, 'verofySAIdentityNumber']
                    ],
                    "license" => [
                        'url' => "",
                        'function' => [$this, 'verifyLicense']
                    ],
                    "passport" => [
                        'url' => "",
                        'function' => [$this, 'verifyPassport']
                    ]
                ];
            default:
                return [
                    "nin" => [
                        'url' => "/v2/api/identity/global/validate",
                        'function' => [$this, 'verifyGlobalIdentityNumber']
                    ],
                    "passport" => [
                        'url' => "/v2/api/identity/global/validate",
                        'function' => [$this, 'verifyGlobalIdentityNumber']
                    ],
                    "license" => [
                        'url' => "/v2/api/identity/global/validate",
                        'function' => [$this, 'verifyGlobalIdentityNumber']
                    ],
                ];
        }
    }

    public function kycVerification(array $data)
    {
        try {
            $countryData = $this->getVerificationUrl($data['country']);
            $documentType = $data['document'];

            if (!isset($countryData[$documentType]['function'])) {
                throw new Error("Verification function not defined for document type: {$documentType}");
            }

            $verificationFunction = $countryData[$documentType]['function'];

            if (!is_callable($verificationFunction)) {
                throw new Error("Verification method for {$documentType} is not callable.");
            }

            return call_user_func($verificationFunction, $data);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Error("An error occurred performing KYC verification");
        }
    }

    public function verifyNIN(array $data)
    {
        Log::info(['data' => $data]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => env('YOUVERIFY_API_KEY'),
        ])->post(env('YOUVERIFY_BASE_URL') . $this->getVerificationUrl($data['country'])['nin']['url'], [
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

        return $response['data']['validations']['selfie']['selfieVerification']['match'];
    }

    public function verifyLicense(array $data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => env('YOUVERIFY_API_KEY'),
        ])->post(env('YOUVERIFY_BASE_URL') . $this->getVerificationUrl($data['country'])['license']['url'], [
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

        return $response['data']['validations']['selfie']['selfieVerification']['match'];
    }

    public function verifyPassport(array $data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => env('YOUVERIFY_API_KEY'),
        ])->post(env('YOUVERIFY_BASE_URL') . $this->getVerificationUrl($data['country'])['passport']['url'], [
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

        return $response['data']['validations']['selfie']['selfieVerification']['match'];
    }

    public function verifyKenyanIdentityNumber(array $data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => env('YOUVERIFY_API_KEY'),
        ])->post(env('YOUVERIFY_BASE_URL') . $this->getVerificationUrl($data['country'])['nin']['url'], [
            'id' => $data['id'],
            'isSubjectConsent' => true,
            'idType' => 'national-id',
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

        return $response['data']['firstName'] == $data['firstName'] && $response['data']['lastName'] == $data['lastName'];
    }

    public function verifyKenyanLicense(array $data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => env('YOUVERIFY_API_KEY'),
        ])->post(env('YOUVERIFY_BASE_URL') . $this->getVerificationUrl($data['country'])['license']['url'], [
            'id' => $data['id'],
            'isSubjectConsent' => true,
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

        return $response['data']['allValidationPassed'];
    }

    public function verofySAIdentityNumber(array $data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => env('YOUVERIFY_API_KEY'),
        ])->post(env('YOUVERIFY_BASE_URL') . $this->getVerificationUrl($data['country'])['nin']['url'], [
            'id' => $data['id'],
            'isSubjectConsent' => true,
            'validations' => [
                'data' => [
                    'lastName' => $data['lastName'],
                    'firstName' => $data['firstName'],
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

        return $response['data']['validations']['data']['lastName']['validated'] && $response['data']['validations']['data']['firstName']['validated'];
    }

    public function verifyGlobalIdentityNumber(array $data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'token' => env('YOUVERIFY_API_KEY'),
        ])->post(env('YOUVERIFY_BASE_URL') . $this->getVerificationUrl($data['country'])['nin']['url'], [
            'id' => $data['id'],
            'isSubjectConsent' => true,
            'advanced' => true,
            "fullName" => $data['firstName'] . " " . $data['lastName'],
            "firstName" => $data['firstName'],
            "lastName" => $data['lastName'],
            'dateOfBirth' => '',
            'mobile' => $data['mobile']
        ])
            ->throw(function (Response $response, RequestException $exception) {
                $message = !empty($response->json()['data']['validations']['validationMessages'])
                    ? $response->json()['data']['validations']['validationMessages']
                    : (!empty($response->json()['message'])
                        ? $response->json()
                        : $exception->getMessage());

                throw new Exception($message);
            })->json();

        if (!$response['success']) {
            Log::error('Unsuccessful request');
            throw new Error('Unsuccessful request');
        }

        return $response['data']['validationDetails']['percentage']['percentageNotMatched'] < 50;
    }
}
