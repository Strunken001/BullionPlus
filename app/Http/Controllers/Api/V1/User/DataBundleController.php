<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Constants\DataBundleConst;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\MobileTopUpHelper;
use App\Http\Helpers\NotificationHelper;
use App\Http\Helpers\PushNotificationHelper;
use App\Http\Helpers\Response;
use App\Models\UserNotification;
use App\Notifications\Admin\ActivityNotification;
use App\Notifications\User\MobileTopup\TopupAutomaticMail;
use App\Providers\Admin\BasicSettingsProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DataBundleController extends Controller
{
    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    public function getReloadlyOperators(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_code'    => 'required|string',
        ]);

        if ($validator->fails()) return Response::error($validator->errors()->all());

        $validated = $validator->validate();

        // make ca cache key for getting future data without api request
        // $cache_key = DataBundleConst::SLUG . "_" . "MOBILE_TOPUP_OPERATORS" . "_" . "$country_code" . "_" . DataBundleConst::TOPUP_BUNDLE;

        // $cache_data = cache()->driver("file")->get($cache_key);

        // if ($cache_data) {
        //     return Response::success([__("Operators Fetch Successfully!")], [
        //         'operators'     => $cache_data,
        //         'cache_key'     => $cache_key,
        //     ]); // if data already cached it will return from here
        // }

        // try {
        //     ini_set('max_execution_time', 180);
        // } catch (Exception $e) {
        // }

        try {
            $get_operators = (new MobileTopUpHelper())->getInstance()->getOperatorsByCountry($validated["country_code"], ["bundlesOnly" => true]);
        } catch (Exception $e) {
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return Response::error([$message], [], 500);
        }

        $operators = collect($get_operators);

        $operators = array_values($operators->where('bundle', true)->toArray());

        // cache data for 20 min
        // cache()->driver("file")->put($cache_key, $operators, 14200);

        // return Response::success([__("Operators Fetch Successfully!")], [
        //     'operators'     => $operators,
        //     // 'cache_key'     => $cache_key,
        // ]);

        return response()->json([
            "message" => "Operators Fetch Successfully!",
            "data" => [
                "operators" => $operators,
                // "cache_key" => $cache_key
            ]
        ]);
    }

    /**
     * return preview view file
     * @param \App\Models\TemporaryData
     * @param string $page_title
     */
    public function getCharges(Request $request)
    {
        $info = $request->all();
        try {
            $charges = (new MobileTopUpHelper())->getInstance()->getCharges($info);
        } catch (Exception $e) {
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return Response::error([$message], [], 500);
        }

        return Response::success([__("Charges Fetch Successfully!")], [
            'charges'     => $charges,
        ]);
    }


    /**
     * bundle request send
     */
    public function buyBundle(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'operator_id'    => 'required',
            'request_amount'    => 'required',
            'geo_location'    => 'required',
            'phone'    => 'required',
            'country_code'    => 'required',
        ]);

        if ($validator->fails()) return Response::error($validator->errors()->all());

        $validated = $validator->validate();
        try {
            $operator = (new MobileTopUpHelper())->getInstance()->getOperator($request->operator_id);
            $trx_ref  = generate_unique_string('transactions', 'trx_id', 16);
            $recharge_country_iso2 = $validated['country_code'];
            $request->merge(['operator' => $request->operator_id, 'amount' => $request->request_amount, 'trx_ref' => $trx_ref, 'recharge_country_iso2' => $recharge_country_iso2]);
            $charges = $charges = (new MobileTopUpHelper())->getInstance()->getCharges($request->all());
            $charges = json_decode(json_encode($charges));
            $request->merge(['operator' => $operator]);
            $topup = (new MobileTopUpHelper())->getInstance()->topup($request);

            $id = $this->insertTransaction($trx_ref, auth()->user()->wallets, $charges, $operator, $topup['response']['recipientPhone'], $topup['response']);
        } catch (Exception $e) {
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return Response::error([$message], [], 500);
        }
        return Response::success([__("Bundle request successful")]);
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
        $afterCharge =  ($authWallet->balance - $charges->total_payable);
        $details = [
            'topup_type'        => DataBundleConst::TOPUP_BUNDLE,
            'topup_type_id'     => $operator['operatorId'] ?? '',
            'topup_type_name'   => $operator['name'] ?? '',
            'mobile_number'     => $mobile_number,
            'topup_amount'      => $charges->exchange_amount ?? 0,
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
                'type'                          => DataBundleConst::TOPUP_BUNDLE,
                'trx_id'                        => $trx_id,
                'request_amount'                => $charges->request_amount,
                'exchange_rate'                 => $charges->exchange_rate,
                'percent_charge'                => $charges->percent_charge,
                'fixed_charge'                  => $charges->fixed_charge,
                'total_charge'                  => $charges->total_charge_calc,
                'request_currency'              => $charges->bundle_currency,
                'total_payable'                 => $charges->total_payable,
                'payment_currency'              => $sender_wallet->currency->code,
                'available_balance'             => $afterCharge,
                'remark'                        => ucwords(remove_speacial_char(DataBundleConst::TOPUP_BUNDLE, " ")) . " Request Successful",
                'details'                       => json_encode($details),
                'callback_ref'                  => $topUpData['customIdentifier'],
                'status'                        => $status,
                'created_at'                    => now(),
            ]);
            $this->updateSenderWalletBalance($authWallet, $afterCharge);



            try {
                $this->insertAutomaticCharges($id, $charges, $sender_wallet, $mobile_number, $trx_id);
                $user = auth()->user();

                // dd($charges);

                if ($this->basic_settings->email_notification == true) {
                    //send notifications
                    $notifyData = [
                        'trx_id'            => $trx_id,
                        'operator_name'     => $operator['name'] ?? '',
                        'mobile_number'     => $mobile_number,
                        'request_amount'    => get_amount($charges->request_amount, $charges->bundle_currency),
                        'exchange_rate'     => get_amount(1, $charges->bundle_currency) . " = " . get_amount($charges->exchange_rate, $charges->wallet_currency_code, 4),
                        'charges'           => get_amount($charges->total_charge_calc, $charges->wallet_currency_code),
                        'payable'           => get_amount($charges->total_payable, $charges->wallet_currency_code),
                        'current_balance'   => get_amount($sender_wallet->balance, $charges->wallet_currency_code),
                        'status'            => __("Successful"),
                    ];
                    try {
                        $user->notify(new TopupAutomaticMail($user, (object)$notifyData));
                    } catch (Exception $e) {
                    }
                }
                //admin notification
                $this->adminNotificationAutomatic($trx_id, $charges, $operator, $user, $mobile_number, $topUpData);
            } catch (Exception $e) {
                return back()->with(['error' => [__("Something went wrong! Please try again.")]]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception(__("Something went wrong! Please try again."));
        }
        return $id;
    }


    public function insertAutomaticCharges($id, $charges, $sender_wallet, $trx_id, $phone)
    {
        DB::beginTransaction();
        try {
            DB::table('transaction_charges')->insert([
                'transaction_id'    =>  $id,
                'percent_charge'    =>  $charges->percent_charge,
                'fixed_charge'      =>  $charges->fixed_charge,
                'total_charge'      =>  $charges->total_charge_calc,
                'created_at'        =>  now(),
            ]);
            DB::commit();

            //notification
            $notification_content = [
                'title'         => __("Data Bundle"),
                'message'       => __('Data Bundle request successful') . " " . $charges->request_amount . ' ' . $charges->bundle_currency,
                'image'         => get_image($sender_wallet->user->image, 'user-profile'),
            ];

            //user Notification
            UserNotification::create([
                'type'      =>  NotificationConst::DATA_BUNDLE,
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
                sendSms($sender_wallet->user, 'Data Bundle', '', ['amount' => $charges->request_amount, 'request_currency' => $charges->bundle_currency, 'mobile_number' => $phone, 'trx' => $trx_id]);
            } catch (Exception $e) {
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception(__("Something went wrong! Please try again."));
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
    public function adminNotificationAutomatic($trx_id, $charges, $operator, $user, $phone, $topUpData)
    {
        $exchange_rate = get_amount(1, $charges->request_amount) . " = " . get_amount($charges->exchange_rate, $charges->wallet_currency_code, 4);
        if (isset($topUpData) && isset($topUpData['status']) && $topUpData['status'] === "SUCCESSFUL") {
            $status = "success";
        } else {
            $status = "Processing";
        }
        $notification_content = [
            //email notification
            'subject' => __("Data Bundle Up For") . " " . $operator['name'] . ' (' . $phone . ' )',
            'greeting' => __("Data Bundle request successful") . " (" . $operator['name'] . "-" . $phone . " )",
            'email_content' => __("trx_id") . " : " . $trx_id . "<br>" . __("Mobile Number") . " : " . $phone . "<br>" . __("Operator Name") . " : " . $operator['name'] . "<br>" . __("request Amount") . " : " . get_amount($charges->request_amount, $charges->bundle_currency) . "<br>" . __("Exchange Rate") . " : " . $exchange_rate . "<br>" . __("Fees & Charges") . " : " . get_amount($charges->total_charge_calc, $charges->bundle_currency) . "<br>" . __("Total Payable Amount") . " : " . get_amount($charges->total_payable, $charges->bundle_currency) . "<br>" . __("Status") . " : " . __($status),

            //push notification
            'push_title' => __("Data Bundle request successful") . " (" . userGuard()['type'] . ")",
            'push_content' => __('trx_id') . " : " . $trx_id . "," . __("request Amount") . " : " . get_amount($charges->request_amount, $charges->bundle_currency) . "," . __("Operator Name") . " : " . $operator['name'] . "," . __("Mobile Number") . " : " . $phone,

            //admin db notification
            'notification_type' =>  NotificationConst::DATA_BUNDLE,
            'admin_db_title' => "Mobile topup request successful" . " (" . userGuard()['type'] . ")",
            'admin_db_message' => "Transaction ID" . " : " . $trx_id . "," . "Request Amount" . " : " . get_amount($charges->request_amount, $charges->bundle_currency) . "," . "Operator Name" . " : " . $operator['name'] . "," . "Mobile Number" . " : " . $phone . "," . "Total Payable Amount" . " : " . get_amount($charges->total_payable, $charges->wallet_currency_code) . " (" . $user->email . ")"
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
