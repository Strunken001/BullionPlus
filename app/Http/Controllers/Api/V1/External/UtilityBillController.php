<?php

namespace App\Http\Controllers\Api\V1\External;

use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\NotificationHelper;
use App\Http\Helpers\PushNotificationHelper;
use App\Http\Helpers\UtilityPaymentHelper;
use App\Http\Helpers\VTPass;
use App\Http\Requests\VerifyMeterNumberRequest;
use App\Models\Transaction;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Models\VTPassAPIDiscount;
use App\Notifications\Admin\ActivityNotification;
use App\Notifications\UtilityPaymentMail;
use App\Providers\Admin\BasicSettingsProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UtilityBillController extends Controller
{
    protected $basic_settings;
    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    public function getUtiityBiller(Request $request)
    {
        if (!$request->iso2) {
            return response()->json([
                "status" => "error",
                "message" => "Query parameter 'iso2' is required"
            ], 400);
        }

        try {
            $billers = (new UtilityPaymentHelper())->getInstance()->getUtilityBillers([
                'countryISOCode' => $request->iso2,
                'page' => $request->page ?? 1,
                'size' => $request->size ?? 20,
            ]);
        } catch (Exception $e) {
            Log::error("An error occured: " . $e->getMessage());
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return response()->json([
                "status" => "success",
                "message" => $message,
                "data" => null,
            ], 500);
        }

        // $bills = [];

        foreach ($billers['content'] as &$b) {
            $b['biller_id'] = $b['id'];
            unset($b['id']);
        }
        unset($b);

        return response()->json([
            'status' => "success",
            'message' => "Utility billers fetched successfully",
            'data' => $billers,
        ]);
    }

    public function payBill(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "biller_id"             => "required",
            'amount'                => "required|numeric|gt:0",
            "account_number"        => "required|string",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                "message" => $validator->errors()->first(),
                "data" => null
            ], 400);
        }

        $api_discount_percentage = $this->basic_settings->api_discount_percentage / 100;

        $request->merge(["api_discount_percentage" => $api_discount_percentage]);

        $charges = (new UtilityPaymentHelper())->getInstance()->getUtilityBillCharge($request->all());
        $charges = json_decode(json_encode($charges));

        $sender_wallet = UserWallet::where('user_id', auth()->id())->first();
        if (!$sender_wallet) {
            return back()->with(['error' => [__('User Wallet not found')]]);
        }

        if ($charges->total_payable > $sender_wallet->balance) {
            return response()->json([
                'status' => 'error',
                'message' => "Sorry, insufficient balance",
                'data' => null
            ], 400);
        }

        try {
            $utility_bill_transaction = null;
            $account_number = null;

            $utility_bill = (new UtilityPaymentHelper())->getInstance()->getUtilityBill($request->biller_id);

            if ($utility_bill["localTransactionCurrencyCode"] === "NGN") {
                $service_id = strtolower(explode(" ", $utility_bill["name"])[0]) . "-electric";
                $variation_code = strtolower($utility_bill["serviceType"]);
                $amount = $request->amount;
                $account_number = $request->account_number;
                $phone = auth()->user()->full_mobile;

                $verify_meter_number = (new VTPass())->verifyMeterNumber([
                    'service_id' => $service_id,
                    'variation_code' => $variation_code,
                    'account_number' => $account_number,
                ]);
                if ($verify_meter_number['content']['WrongBillersCode']) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'invalid number',
                        'data' => null
                    ]);
                }

                // $api_discount_percentage = $this->basic_settings->api_discount_percentage / 100;
                // $vtpass_discount = VTPassAPIDiscount::where('service_id', $service_id)->first();
                // $provider_discount_amount = ($vtpass_discount->api_discount_percentage / 100) * $amount;
                // $discount_price_amount = (1 - $api_discount_percentage) * $provider_discount_amount;

                $request['amount'] = $amount;

                $payment = (new VTPass())->utilityPayment([
                    'service_id' => $service_id,
                    'variation_code' => $variation_code,
                    'amount' => $request->amount,
                    'account_number' => $account_number,
                    'phone' => $phone
                ]);

                $tx_ref = $payment['requestId'];

                $utility_bill_transaction = (new VTPass())->verifyUtilityPaymentTransaction($tx_ref);
            } else {
                $request['tx_ref'] = generate_unique_string('transactions', 'trx_id', 16);
                $request['useLocalAmount'] = true;

                $utility_bill = (new UtilityPaymentHelper())->getInstance()->getUtilityBill($request->biller_id);

                // $api_discount_percentage = $this->basic_settings->api_discount_percentage / 100;
                // $provider_discount_amount = ($utility_bill['internationalDiscountPercentage'] / 100) * $request->amount;
                // $discount_price_amount = (1 - $api_discount_percentage) * $provider_discount_amount;

                $request['amount'] = $request->amount;

                $payment = (new UtilityPaymentHelper())->getInstance()->payBill($request->all());
                $tx_ref = $payment['referenceId'];

                sleep(20);

                $utility_bill_transaction = (new UtilityPaymentHelper())->getInstance()->getUtilityBillTransaction($tx_ref);

                $account_number = $utility_bill_transaction['transaction']['billDetails']['subscriberDetails']['accountNumber'];
            }

            if (isset($utility_bill_transaction) && isset($utility_bill_transaction['transaction']['status']) && $utility_bill_transaction['transaction']['status'] !== "SUCCESSFUL") {
                return response()->json([
                    "status" => "error",
                    "message" => "Unable to complete transaction at the moment. Please try again later'",
                    "data" => null
                ], 400);
            }

            $this->insertTransaction($tx_ref, auth()->user()->wallets, $charges, $utility_bill_transaction, $account_number);
        } catch (Exception $e) {
            Log::error("An error occured: " . $e->getMessage());
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return response()->json([
                "status" => "error",
                "message" => $message,
                "data" => null
            ], 500);
        }

        return response()->json([
            "status" => "success",
            "message" => "Utility bill payment request successful",
            "data" => [
                "status" => $utility_bill_transaction['transaction']['status'],
                "product_name" => $utility_bill_transaction['transaction']['product_name'],
                "unique_element" => $utility_bill_transaction['transaction']['unique_element'],
                "total_amount" => $utility_bill_transaction['transaction']['total_amount'],
                "type" => $utility_bill_transaction['transaction']['type'],
                "transactionId" => $tx_ref,
                "billDetails" => $utility_bill_transaction['transaction']['billDetails'],
            ]
        ]);
    }

    public function insertTransaction($trx_id, $sender_wallet, $charges, $utility_bill_transaction, $account_number)
    {
        if (isset($utility_bill_transaction) && isset($utility_bill_transaction['transaction']['status']) && $utility_bill_transaction['transaction']['status'] === "SUCCESSFUL") {
            $status = PaymentGatewayConst::STATUSSUCCESS;
        } else if (isset($utility_bill_transaction) && isset($utility_bill_transaction['transaction']['status']) && $utility_bill_transaction['transaction']['status'] === "PROCESSING") {
            $status = PaymentGatewayConst::STATUSPENDING;
        }

        $trx_id = $trx_id;
        $authWallet = $sender_wallet;
        $afterCharge =  ($authWallet->balance - $charges->total_payable);
        $details = [
            'charges'               => $charges,
            'api_response'          => $utility_bill_transaction ?? [],
        ];
        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id'                       => $sender_wallet->user->id,
                'wallet_id'                     => $authWallet->id,
                'payment_gateway_currency_id'   => null,
                'type'                          => NotificationConst::BILL_PAY,
                'trx_id'                        => $trx_id,
                'request_amount'                => $charges->amount,
                'exchange_rate'                 => $charges->rate,
                'percent_charge'                => $charges->percent_charge,
                'fixed_charge'                   => $charges->fixed_charge,
                'total_charge'                  => $charges->total_charge_calc,
                'request_currency'              => $charges->bundle_currency,
                'total_payable'                 => $charges->total_payable,
                'payment_currency'              => $sender_wallet->currency->code,
                'available_balance'             => $afterCharge,
                'remark'                        => ucwords(remove_speacial_char($utility_bill_transaction['transaction']['billDetails']['type'], " ")) . " Request Successful",
                'details'                       => json_encode($details),
                'status'                        => $status,
                'created_at'                    => now(),
            ]);
            $this->updateSenderWalletBalance($authWallet, $afterCharge);

            try {
                $this->insertAutomaticCharges($transaction->id, $charges, $sender_wallet, $account_number, $trx_id);
                $user = auth()->user();

                // dd($charges);

                if ($this->basic_settings->email_notification == true) {
                    //send notifications
                    $notifyData = [
                        'trx_id'            => $trx_id,
                        'operator_name'     => $operator['name'] ?? '',
                        'account_number'    => $account_number,
                        'request_amount'    => get_amount($charges->amount, $charges->bundle_currency),
                        'exchange_rate'     => get_amount($charges->rate, $charges->wallet_currency_code, 4),
                        'charges'           => get_amount($charges->total_charge_calc, $charges->wallet_currency_code),
                        'payable'           => get_amount($charges->total_payable, $charges->wallet_currency_code),
                        'previous_balance'  => get_amount($sender_wallet->balance + $charges->total_payable, $charges->wallet_currency_code),
                        'current_balance'   => get_amount($sender_wallet->balance, $charges->wallet_currency_code),
                        'token'             => $utility_bill_transaction['transaction']['billDetails']['pinDetails']['token'] ?? "--",
                        'date'              => $transaction->created_at,
                    ];
                    try {
                        $user->notify(new UtilityPaymentMail($user, (object)$notifyData));
                    } catch (Exception $e) {
                        Log::error("An error occured sending email:" . $e->getMessage());
                    }
                }
                //admin notification
                $this->adminNotificationAutomatic($trx_id, $charges, $user, $account_number, $utility_bill_transaction);
            } catch (Exception $e) {
                Log::error("Utility Bill Payment Error: " . $e->getMessage());

                return response()->json([
                    "status" => "error",
                    "message" => "Something went wrong! Please try again.",
                    "data" => null
                ], 500);
            }

            DB::commit();
        } catch (Exception $e) {
            Log::error("Utility Bill Payment Error: " . $e->getMessage());
            DB::rollBack();
            return response()->json([
                "status" => "error",
                "message" => "Something went wrong! Please try again.",
                "data" => null
            ], 500);
        }
        return $transaction->id;
    }

    public function insertAutomaticCharges($id, $charges, $sender_wallet, $account_number, $trx_id)
    {
        DB::beginTransaction();
        try {
            DB::table('transaction_charges')->insert([
                'transaction_id'    =>  $id,
                'percent_charge'    =>  $charges->percent_charge_calc,
                'fixed_charge'       =>  $charges->fixed_charge,
                'total_charge'      =>  $charges->total_charge_calc,
                'created_at'        =>  now(),
            ]);
            DB::commit();

            //notification
            $notification_content = [
                'title'         => __("Utility Bill"),
                'message'       => __('Utility Payment request successful') . " " . $charges->amount . ' ' . $charges->currency_code,
                'image'         => get_image($sender_wallet->user->image, 'user-profile'),
            ];

            //user Notification
            UserNotification::create([
                'type'      =>  $charges->type,
                'user_id'   =>  $sender_wallet->user->id,
                'message'   =>  $notification_content,
            ]);

            try {
                // Push Notification
                (new PushNotificationHelper())->prepare([$sender_wallet->user->id], [
                    'title' => $notification_content['title'],
                    'desc'  => $notification_content['message'],
                    'user_type' => 'user',
                ])->send();
                sendSms($sender_wallet->user, 'Utility Payment', '', ['amount' => $charges->amount, 'request_currency' => $charges->currency_code, 'account_number' => $account_number, 'trx' => $trx_id]);
            } catch (Exception $e) {
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "status" => "error",
                "message" => "Something went wrong! Please try again.",
                "data" => null
            ], 500);
        }
    }

    //end automatic
    public function updateSenderWalletBalance($authWallet, $afterCharge)
    {
        $authWallet->update([
            'balance'   => $afterCharge,
        ]);
    }

    //admin notification
    public function adminNotificationAutomatic($trx_id, $charges, $user, $account_number, $utility_bill_transaction)
    {
        $exchange_rate = get_amount(1, $charges->amount) . " = " . get_amount($charges->rate, $charges->wallet_currency_code, 4);
        if (isset($utility_bill_transaction) && isset($utility_bill_transaction['transaction']['status']) && $utility_bill_transaction['transaction']['status'] === "SUCCESSFUL") {
            $status = PaymentGatewayConst::STATUSSUCCESS;
        } else if (isset($utility_bill_transaction) && isset($utility_bill_transaction['transaction']['status']) && $utility_bill_transaction['transaction']['status'] === "PROCESSING") {
            $status = PaymentGatewayConst::STATUSPENDING;
        }

        $notification_content = [
            //email notification
            'subject' => __("Utility Payment For") . " " . $charges->name . ' (' . $account_number . ' )',
            'greeting' => __("Utility Payment request successful") . " (" . $charges->name . "-" . $account_number . " )",
            'email_content' => __("trx_id") . " : " . $trx_id . "<br>" . __("Account Number") . " : " . $account_number . "<br>" . __("Utility Bill") . " : " . $charges->name . "<br>" . __("request Amount") . " : " . get_amount($charges->amount, $charges->bundle_currency) . "<br>" . __("Exchange Rate") . " : " . $exchange_rate . "<br>" . __("Fees & Charges") . " : " . get_amount($charges->total_charge_calc, $charges->bundle_currency) . "<br>" . __("Total Payable Amount") . " : " . get_amount($charges->total_payable, $charges->bundle_currency) . "<br>" . __("Status") . " : " . __($status),

            //push notification
            'push_title' => __("Utility Payment request successful") . " (" . userGuard()['type'] . ")",
            'push_content' => __('trx_id') . " : " . $trx_id . "," . __("request Amount") . " : " . get_amount($charges->amount, $charges->bundle_currency) . "," . __("Operator Name") . " : " . $charges->name . "," . __("Account Number") . " : " . $account_number,

            //admin db notification
            'notification_type' =>  NotificationConst::BILL_PAY,
            'admin_db_title' => "Mobile topup request successful" . " (" . userGuard()['type'] . ")",
            'admin_db_message' => "Transaction ID" . " : " . $trx_id . "," . "Request Amount" . " : " . get_amount($charges->amount, $charges->bundle_currency) . "," . "Operator Name" . " : " . $charges->name . "," . "Account Number" . " : " . $account_number . "," . "Total Payable Amount" . " : " . get_amount($charges->total_payable, $charges->wallet_currency_code) . " (" . $user->email . ")"
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

    public function verifyMeterNumber(VerifyMeterNumberRequest $request)
    {
        $verify_meter_number = (new VTPass())->verifyMeterNumber([
            'service_id' => $request->service_id,
            'variation_code' => $request->variation_code,
            'account_number' => $request->account_number,
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'invalid number',
            'data' => $verify_meter_number['content']
        ]);
    }
}
