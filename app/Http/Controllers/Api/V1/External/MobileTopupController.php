<?php

namespace App\Http\Controllers\Api\V1\External;

use App\Constants\GlobalConst;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AirtimeHelper;
use App\Http\Helpers\Api\helpers as Helpers;
use App\Http\Helpers\NotificationHelper;
use App\Models\Admin\ExchangeRate;
use App\Models\Admin\TransactionSetting;
use App\Models\TopupCategory;
use App\Models\Transaction;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Notifications\Admin\ActivityNotification;
use App\Notifications\User\MobileTopup\TopupAutomaticMail;
use App\Notifications\User\MobileTopup\TopupMail;
use App\Providers\Admin\BasicSettingsProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Helpers\PushNotificationHelper;
use App\Http\Helpers\VTPass;
use App\Models\VTPassAPIDiscount;
use Illuminate\Support\Facades\Log;

class MobileTopupController extends Controller
{
    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    //Start Automatic
    public function checkOperator()
    {
        $validator = Validator::make(request()->all(), [
            'mobile_code' => 'required',
            'mobile_number' => 'required',
            'country_code' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                "message" => $validator->errors()->first(),
                "data" => null
            ], 400);
        }

        $validated = $validator->validate();
        $mobile_code = remove_special_char($validated['mobile_code']);
        $mobile = remove_special_char($validated['mobile_number']);
        $phone = $mobile_code . $mobile;
        $iso = $validated['country_code'];
        $operator = (new AirtimeHelper())->autoDetectOperator($phone, $iso);

        if ($operator['status'] === false) {
            return response()->json([
                'status' => "error",
                'message' => $operator['message'] ?? "",
                'data' => null,
            ], 400);
        }

        $operator['receiver_currency_rate'] = getAmount(receiver_currency($operator['destinationCurrencyCode'])['rate'], 2);
        $operator['receiver_currency_code'] = receiver_currency($operator['destinationCurrencyCode'])['currency'];
        $operator['trx_info'] = TransactionSetting::where('slug', 'mobile_topup')->first()->only(['fixed_charge', 'percent_charge', 'min_limit', 'max_limit', 'monthly_limit', 'daily_limit']);

        return response()->json([
            'status' => "success",
            'message' => 'Successfully Get Operator',
            // 'data' => [
            //     'operator_id' => $operator['operatorId'],
            //     'name' => $operator['name'],
            //     'country' => $operator['country'],
            //     'logoUrls' => $operator['logoUrls'],
            // ],
            'data' => $operator,
        ], 200);
    }

    public function payAutomatic(Request $request)
    {
        try {
            $validator = Validator::make(request()->all(), [
                'operator_id' => 'required',
                'mobile_code' => 'required',
                'mobile_number' => 'required|min:6|max:15',
                'country_code' => 'required',
                'amount' => 'required|numeric|gt:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    "message" => $validator->errors()->first(),
                    "data" => null
                ], 400);
            }

            $validated = $validator->validate();

            $user = auth()->user();
            $sender_phone = $user->full_mobile ?? "";
            $sender_country_name = @$user->address->country;
            $foundItem = '';
            foreach (get_all_countries(GlobalConst::USER) ?? [] as $item) {
                if ($item->name === $sender_country_name) {
                    $foundItem = $item;
                }
            }

            $sender_country_iso = $foundItem->iso2;
            $phone = remove_special_char($validated['mobile_code']) . $validated['mobile_number'];
            $operator = (new AirtimeHelper())->autoDetectOperator($phone, $validated['country_code']);
            if ($operator['status'] === false) {
                return response()->json([
                    "status" => "error",
                    "message" => $operator['message'] ?? "",
                    "data" => null
                ], 500);
            }

            $sender_wallet = UserWallet::where('user_id', $user->id)->first();
            if (!$sender_wallet) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User Wallet not found',
                    'data' => null
                ], 404);
            }

            $topupCharge = TransactionSetting::where('slug', 'mobile_topup')->where('status', 1)->first();
            $api_discount_percentage = $this->basic_settings->api_discount_percentage / 100;
            $charges = $this->topupChargeAutomatic($validated['amount'], $operator, $sender_wallet, $topupCharge, $api_discount_percentage);

            if ($charges['payable'] > $sender_wallet->balance) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Sorry, insufficient balance",
                    'data' => null
                ], 400);
            }

            $topUpData = [];

            if ($request->country_code === "NG") {
                $network_provider = $operator['name'] ?? null;

                $vtpass_service_id = explode(" ", $network_provider)[0];

                $service_id = $vtpass_service_id == "9Mobile" ? "etisalat" : strtolower($vtpass_service_id);

                // $api_discount_percentage = $this->basic_settings->api_discount_percentage / 100;
                // $vtpass_discount = VTPassAPIDiscount::where('service_id', $service_id)->first();
                // $provider_discount_amount = ($vtpass_discount->api_discount_percentage / 100) * $request->amount;
                // $discount_price_amount = (1 - $api_discount_percentage) * $provider_discount_amount;

                $validated['amount'] = $validated['amount'];

                $topUpData = [
                    "service_id" => $service_id,
                    "amount" => $validated['amount'],
                    "phone" => $validated['mobile_number'],
                    "customIdentifier" => Str::uuid() . "|" . "AIRTIME",
                ];

                $topUpData = (new VTPass())->mobileTopUp($topUpData);
            } else {
                // $api_discount_percentage = $this->basic_settings->api_discount_percentage / 100;
                // $provider_discount_amount = ($operator['internationalDiscount'] / 100) * $request->amount;
                // $discount_price_amount = (1 - $api_discount_percentage) * $provider_discount_amount;

                $validated['amount'] = $validated['amount'];
                //topup api
                $topUpData = [
                    'operatorId'        => $operator['operatorId'],
                    'amount'            => $validated['amount'],
                    'useLocalAmount'    => $operator['supportsLocalAmounts'],
                    'customIdentifier'  => Str::uuid() . "|" . "AIRTIME",
                    'recipientEmail'    => null,
                    'recipientPhone'  => [
                        'countryCode' => $validated['country_code'],
                        'number'  => $phone,
                    ],
                    'senderPhone'   => [
                        'countryCode' => $sender_country_iso,
                        'number'      => $sender_phone,
                    ]

                ];

                $topUpData = (new AirtimeHelper())->makeTopUp($topUpData);
            }

            if (isset($topUpData['status']) && ($topUpData['status'] === false || $topUpData['status'] !== "SUCCESSFUL")) {
                return response()->json([
                    'status' => 'error',
                    'message' => $topUpData['message'] ?? 'Something went wrong! Please try again.',
                    'data' => null
                ], 500);
            }

            if ($operator['denominationType'] === "RANGE") {
                $min_amount = 0;
                $max_amount = 0;
                if ($operator["supportsLocalAmounts"] == true && $operator["destinationCurrencyCode"] == $operator["senderCurrencyCode"] && $operator["localMinAmount"] == null && $operator["localMaxAmount"] == null) {
                    $min_amount = $operator['minAmount'];
                    $max_amount = $operator['maxAmount'];
                } else if ($operator["supportsLocalAmounts"] == true && $operator["localMinAmount"] != null && $operator["localMaxAmount"] != null) {
                    $min_amount = $operator["localMinAmount"];
                    $max_amount = $operator["localMaxAmount"];
                } else {
                    $min_amount = $operator['minAmount'];
                    $max_amount = $operator['maxAmount'];
                }
                if ($charges['sender_amount'] < $min_amount || $charges['sender_amount'] > $max_amount) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Please follow the transaction limit",
                        'data' => null
                    ], 400);
                }
            }

            $trx_id = 'MP' . getTrxNum();
            $sender = $this->insertTransaction($trx_id, $sender_wallet, $charges, $operator, $phone, $topUpData);
            $this->insertAutomaticCharges($sender, $charges, $sender_wallet);
            if ($this->basic_settings->email_notification == true) {
                //send notifications
                $notifyData = [
                    'trx_id'            => $trx_id,
                    'operator_name'     => $operator['name'] ?? '',
                    'mobile_number'     => $phone,
                    'request_amount'    => get_amount($charges['sender_amount'], $charges['destination_currency']),
                    'exchange_rate'     => get_amount(1, $charges['destination_currency']) . " = " . get_amount($charges['exchange_rate'], $charges['sender_currency'], 4),
                    'charges'           => get_amount($charges['total_charge'], $charges['sender_currency']),
                    'payable'           => get_amount($charges['payable'], $charges['sender_currency']),
                    'current_balance'   => get_amount($sender_wallet->balance, $charges['sender_currency']),
                    'status'            => __("Successful"),
                ];
                try {
                    $user->notify(new TopupAutomaticMail($user, (object)$notifyData));
                } catch (Exception $e) {
                }
            }
            //admin notification
            $this->adminNotificationAutomatic($trx_id, $charges, $operator, $user, $phone, $topUpData);

            return response()->json([
                'status' => 'success',
                'message' => "Mobile topup request successful",
                'data' => [
                    'transaction_id' => $trx_id,
                    'mobile_number' => $phone,
                    'amount' => $request->amount,
                    'operator' => $operator['name'] ?? '',
                ]
            ], 200);
        } catch (\Throwable $e) {
            Log::error("An error occured: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => "Something went wrong! Please try again.",
                'data' => null
            ], $e->getCode() ?? 500);
        }
    }

    public function insertTransaction($trx_id, $sender_wallet, $charges, $operator, $mobile_number, $topUpData)
    {
        if (isset($topUpData) && isset($topUpData['status']) && $topUpData['status'] === "SUCCESSFUL") {
            $status = PaymentGatewayConst::STATUSSUCCESS;
        } else {
            $status = PaymentGatewayConst::STATUSPROCESSING;
        }
        $trx_id = $trx_id;
        $authWallet = $sender_wallet;
        $afterCharge =  ($authWallet->balance - $charges['payable']);
        $details = [
            'topup_type'        => PaymentGatewayConst::AUTOMATIC,
            'topup_type_id'     => $operator['operatorId'] ?? '',
            'topup_type_name'   => $operator['name'] ?? '',
            'mobile_number'     => $mobile_number,
            'topup_amount'      => $charges['sender_amount'] ?? 0,
            'charges'           => $charges,
            'operator'          => $operator ?? [],
            'api_response'      => $topUpData ?? [],
        ];
        DB::beginTransaction();
        try {
            $id = DB::table("transactions")->insertGetId([
                'user_id'                       => $sender_wallet->user->id,
                'wallet_id'                     => $authWallet->id,
                'payment_gateway_currency_id'   => null,
                'type'                          => PaymentGatewayConst::MOBILETOPUP,
                'trx_id'                        => $trx_id,
                'request_amount'                => $charges['sender_amount'],
                'exchange_rate'                 => 1 / $charges['destination_currency_rate'],
                'percent_charge'                => $charges['percent_charge'],
                'fixed_charge'                  => $charges['fixed_charge'],
                'total_charge'                  => $charges['total_charge'],
                'request_currency'              => $charges['destination_currency'],
                'total_payable'                 => $charges['payable'],
                'available_balance'             => $afterCharge,
                'remark'                        => ucwords(remove_special_char(PaymentGatewayConst::MOBILETOPUP, " ")) . " Request Successful",
                'details'                       => json_encode($details),
                'callback_ref'                  => $topUpData['customIdentifier'],
                'status'                        => $status,
                'created_at'                    => now(),
            ]);
            $this->updateSenderWalletBalance($authWallet, $afterCharge);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $error = ['error' => [__("Something went wrong! Please try again.")]];
            return Helpers::error($error);
        }
        return $id;
    }

    public function insertAutomaticCharges($id, $charges, $sender_wallet)
    {
        DB::beginTransaction();
        try {
            DB::table('transaction_charges')->insert([
                'transaction_id'    =>  $id,
                'percent_charge'    =>  $charges['percent_charge'],
                'fixed_charge'      =>  $charges['fixed_charge'],
                'total_charge'      =>  $charges['total_charge'],
                'created_at'        =>  now(),
            ]);
            DB::commit();

            //notification
            $notification_content = [
                'title'         => __("Mobile Topup"),
                'message'       => __('Mobile topup request successful') . " " . $charges['sender_amount'] . ' ' . $charges['destination_currency'],
                'image'         => get_image($sender_wallet->user->image, 'user-profile'),
            ];

            //user Notification
            UserNotification::create([
                'type'      =>  NotificationConst::MOBILE_TOPUP,
                'user_id'   =>  $sender_wallet->user->id,
                'message'   =>  $notification_content,
            ]);
            //Push Notification
            if ($this->basic_settings->push_notification == true) {
                try {
                    (new PushNotificationHelper())->prepareApi([$sender_wallet->user->id], [
                        'title' => $notification_content['title'],
                        'desc'  => $notification_content['message'],
                        'user_type' => 'user',
                    ])->send();
                } catch (Exception $e) {
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            $error = ['error' => [__("Something went wrong! Please try again.")]];
            return Helpers::error($error);
        }
    }

    public function topupChargeAutomatic($sender_amount, $operator, $sender_wallet, $charges, $api_discount_percentage = 0)
    {
        $destinationCurrency = ExchangeRate::where(['currency_code' => $operator['destinationCurrencyCode']])->first();
        $exchange_rate = $sender_wallet->currency->rate / $destinationCurrency->rate;

        $data['exchange_rate']                     = $exchange_rate;
        $data['sender_amount']                      = $sender_amount;
        $data['sender_currency']                    = $sender_wallet->currency->code;
        $data['sender_currency_rate']               = $sender_wallet->currency->rate;
        $data['destination_currency']               = $destinationCurrency->currency_code;
        $data['destination_currency_rate']          = $destinationCurrency->rate;
        $data['conversion_amount']                  = $sender_amount * $exchange_rate;
        $data['percent_charge']                     = ($sender_amount * $exchange_rate / 100) * $charges->percent_charge ?? 0;
        $data['fixed_charge']                       = $sender_wallet->currency->rate * $charges->fixed_charge ?? 0;
        $data['total_charge']                       = $data['percent_charge'] + $data['fixed_charge'];
        $data['sender_wallet_balance']              = $sender_wallet->balance;
        $discount_amount                          = ($data['conversion_amount'] + $data['total_charge']) * $api_discount_percentage;
        $data['payable']                            = ($data['conversion_amount'] + $data['total_charge']) - $discount_amount;
        return $data;
    }

    //End Automatic
    public function updateSenderWalletBalance($authWallet, $afterCharge)
    {
        $authWallet->update([
            'balance'   => $afterCharge,
        ]);
    }

    //admin notification
    public function adminNotificationManual($trx_id, $charges, $topup_type, $user, $phone)
    {
        $exchange_rate = get_amount(1, $charges['destination_currency']) . " = " . get_amount($charges['exchange_rate'], $charges['sender_currency'], 4);
        $notification_content = [
            //email notification
            'subject' => __("Mobile Top Up For") . " " . $topup_type->name . ' (' . $phone . ' )',
            'greeting' => __("Mobile topup request send to admin successful") . " (" . $topup_type->name . "-" . $phone . " )",
            'email_content' => __("web_trx_id") . " : " . $trx_id . "<br>" . __("Mobile Number") . " : " . $phone . "<br>" . __("Operator Name") . " : " . $topup_type->name . "<br>" . __("request Amount") . " : " . get_amount($charges['sender_amount'], $charges['destination_currency']) . "<br>" . __("Exchange Rate") . " : " . $exchange_rate . "<br>" . __("Fees & Charges") . " : " . get_amount($charges['total_charge'], $charges['sender_currency']) . "<br>" . __("Total Payable Amount") . " : " . get_amount($charges['payable'], $charges['sender_currency']) . "<br>" . __("Status") . " : " . __("Pending"),

            //push notification
            'push_title' => __("Mobile topup request send to admin successful") . " (" . userGuard()['type'] . ")",
            'push_content' => __('web_trx_id') . " : " . $trx_id . "," . __("request Amount") . " : " . get_amount($charges['sender_amount'], $charges['destination_currency']) . "," . __("Operator Name") . " : " . $topup_type->name . "," . __("Mobile Number") . " : " . $phone,

            //admin db notification
            'notification_type' =>  NotificationConst::MOBILE_TOPUP,
            'admin_db_title' => "Mobile topup request send to admin successful" . " (" . userGuard()['type'] . ")",
            'admin_db_message' => "Transaction ID" . " : " . $trx_id . "," . "Request Amount" . " : " . get_amount($charges['sender_amount'], $charges['destination_currency']) . "," . "Operator Name" . " : " . $topup_type->name . "," . "Mobile Number" . " : " . $phone . "," . "Total Payable Amount" . " : " . get_amount($charges['payable'], $charges['sender_currency']) . " (" . $user->email . ")"
        ];
        try {
            //notification
            (new NotificationHelper())->admin(['admin.mobile.topup.index', 'admin.mobile.topup.pending', 'admin.mobile.topup.processing', 'admin.mobile.topup.complete', 'admin.mobile.topup.canceled', 'admin.mobile.topup.details', 'admin.mobile.topup.approved', 'admin.mobile.topup.rejected', 'admin.mobile.topup.export.data'])
                ->mail(ActivityNotification::class, [
                    'subject'   => $notification_content['subject'],
                    'greeting'  => $notification_content['greeting'],
                    'content'   => $notification_content['email_content'],
                ])
                ->push([
                    'user_type' => "admin",
                    'title' => $notification_content['push_title'],
                    'desc'  => $notification_content['push_content'],
                ])
                ->adminDbContent([
                    'type' => $notification_content['notification_type'],
                    'title' => $notification_content['admin_db_title'],
                    'message'  => $notification_content['admin_db_message'],
                ])
                ->send();
        } catch (Exception $e) {
        }
    }

    public function adminNotificationAutomatic($trx_id, $charges, $operator, $user, $phone, $topUpData)
    {
        $exchange_rate = get_amount(1, $charges['destination_currency']) . " = " . get_amount($charges['exchange_rate'], $charges['sender_currency'], 4);
        if (isset($topUpData) && isset($topUpData['status']) && $topUpData['status'] === "SUCCESSFUL") {
            $status = "success";
        } else {
            $status = "Processing";
        }
        $notification_content = [
            //email notification
            'subject' => __("Mobile Top Up For") . " " . $operator['name'] . ' (' . $phone . ' )',
            'greeting' => __("Mobile topup request successful") . " (" . $operator['name'] . "-" . $phone . " )",
            'email_content' => __("web_trx_id") . " : " . $trx_id . "<br>" . __("Mobile Number") . " : " . $phone . "<br>" . __("Operator Name") . " : " . $operator['name'] . "<br>" . __("request Amount") . " : " . get_amount($charges['sender_amount'], $charges['destination_currency']) . "<br>" . __("Exchange Rate") . " : " . $exchange_rate . "<br>" . __("Fees & Charges") . " : " . get_amount($charges['total_charge'], $charges['sender_currency']) . "<br>" . __("Total Payable Amount") . " : " . get_amount($charges['payable'], $charges['sender_currency']) . "<br>" . __("Status") . " : " . __($status),

            //push notification
            'push_title' => __("Mobile topup request successful") . " (" . userGuard()['type'] . ")",
            'push_content' => __('web_trx_id') . " : " . $trx_id . "," . __("request Amount") . " : " . get_amount($charges['sender_amount'], $charges['destination_currency']) . "," . __("Operator Name") . " : " . $operator['name'] . "," . __("Mobile Number") . " : " . $phone,

            //admin db notification
            'notification_type' =>  NotificationConst::MOBILE_TOPUP,
            'admin_db_title' => "Mobile topup request successful" . " (" . userGuard()['type'] . ")",
            'admin_db_message' => "Transaction ID" . " : " . $trx_id . "," . "Request Amount" . " : " . get_amount($charges['sender_amount'], $charges['destination_currency']) . "," . "Operator Name" . " : " . $operator['name'] . "," . "Mobile Number" . " : " . $phone . "," . "Total Payable Amount" . " : " . get_amount($charges['payable'], $charges['sender_currency']) . " (" . $user->email . ")"
        ];
        try {
            //notification
            (new NotificationHelper())->admin(['admin.mobile.topup.index', 'admin.mobile.topup.pending', 'admin.mobile.topup.processing', 'admin.mobile.topup.complete', 'admin.mobile.topup.canceled', 'admin.mobile.topup.details', 'admin.mobile.topup.approved', 'admin.mobile.topup.rejected', 'admin.mobile.topup.export.data'])
                ->mail(ActivityNotification::class, [
                    'subject'   => $notification_content['subject'],
                    'greeting'  => $notification_content['greeting'],
                    'content'   => $notification_content['email_content'],
                ])
                ->push([
                    'user_type' => "admin",
                    'title' => $notification_content['push_title'],
                    'desc'  => $notification_content['push_content'],
                ])
                ->adminDbContent([
                    'type' => $notification_content['notification_type'],
                    'title' => $notification_content['admin_db_title'],
                    'message'  => $notification_content['admin_db_message'],
                ])
                ->send();
        } catch (Exception $e) {
        }
    }
}
