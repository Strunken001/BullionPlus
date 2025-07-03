<?php

namespace App\Http\Controllers\User;

use App\Constants\DataBundleConst;
use Exception;
use Illuminate\Http\Request;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\MobileTopUpHelper;
use App\Http\Helpers\NotificationHelper;
use App\Http\Helpers\PushNotificationHelper;
use App\Http\Helpers\Response;
use App\Http\Helpers\VTPass;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Models\VTPassAPIDiscount;
use App\Notifications\Admin\ActivityNotification;
use App\Notifications\User\MobileTopup\TopupAutomaticMail;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataBundleController extends Controller
{

    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    public function index()
    {
        $page_title = "Data Bundle";
        return view('user.page.bundle', compact('page_title'));
    }

    public function getReloadlyOperators(Request $request)
    {
        $country_code = $request->iso2;

        try {
            $get_operators = null;
            if ($country_code === "NG") {
                $get_operators = VTPassAPIDiscount::where("type", "data_bundle")->get();
            } else {
                $get_operators = (new MobileTopUpHelper())->getInstance()->getOperatorsByCountry($country_code, ['dataOnly' => true]);
            }
        } catch (Exception $e) {
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return Response::error([$message], [], 500);
        }

        return response()->json([
            "message" => "Operators Fetch Successfully!",
            "data" => $get_operators
        ]);
    }

    /**
     * return preview view file
     * @param \App\Models\TemporaryData
     * @param string $page_title
     */
    public function preview(Request $request)
    {
        $info = $request->all();
        try {
            if ($request->mobile_code === "NG") {
                $charges = (new VTPass())->getCharges($info);
            } else {
                $charges = (new MobileTopUpHelper())->getInstance()->getCharges($info);
            }
        } catch (Exception $e) {
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return Response::error([$message], [], 500);
        }
        return view('user.page.bundle-preview', compact('info', 'charges'));
    }

    /**
     * bundle request send
     */
    public function buyBundle(Request $request)
    {
        try {
            $sender_wallet = UserWallet::where('user_id', auth()->id())->first();
            if (!$sender_wallet) {
                return back()->with(['error' => [__('User Wallet not found')]]);
            }

            $info = json_decode($request->info);
            $charges = json_decode($request->charges);

            if ($charges['total_payable'] > $sender_wallet->balance) {
                return back()->with(['error' => [__("Sorry, insufficient balance")]]);
            }

            if ($request->iso2 === "NG") {
                $topup = (new VTPass())->dataBundleTopUp([
                    'service_id' => $request->service_id,
                    'variation_code' => $request->variation_code,
                    'phone' => $info->mobile_number,
                ]);

                $trx_ref = $topup['requestId'];
                $operator = [
                    'operatorId' => $request->service_id,
                    'name' => $topup['transactions']['product_name'],
                ];
            } else {
                $operator = (new MobileTopUpHelper())->getInstance()->getOperator($request->operator_id);
                $trx_ref  = generate_unique_string('transactions', 'trx_id', 16);
                $recharge_country_iso2 = $request->iso2;
                $request->merge(['operator' => $operator, 'trx_ref' => $trx_ref, 'recharge_country_iso2' => $recharge_country_iso2]);
                $topup = (new MobileTopUpHelper())->getInstance()->topup($request);
            }


            if (isset($topup['status']) && ($topup['status'] === false || $topup['status'] !== "SUCCESSFUL")) {
                return redirect()->route("user.data.bundle.index")->with(['error' => [$topup['message']]]);
            }

            $this->insertTransaction($trx_ref, auth()->user()->wallets, $charges, $operator, $topup['response']['recipientPhone'], $topup['response']);
        } catch (Exception $e) {
            Log::error('Data Bundle Error: ' . $e->getMessage());
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return redirect()->route("user.dashboard")->with(['error' => [__($message)]]);
        }
        return redirect()->route("user.dashboard")->with(['success' => [__('Bundle request successful')]]);
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
                'type'                          => NotificationConst::DATA_BUNDLE,
                'trx_id'                        => $trx_id,
                'request_amount'                => $charges->request_amount,
                'exchange_rate'                 => $charges->exchange_rate,
                'percent_charge'                => $charges->percent_charge,
                'fixed_charge'                   => $charges->fixed_charge,
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
                'percent_charge'    =>  $charges->percent_charge_calc,
                'fixed_charge'       =>  $charges->fixed_charge,
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

    /**
     * Receive webhook response
     * @param \Illuminate\Http\Request $request
     */
    public function receiveWebhook(Request $request)
    {
        return (new MobileTopUpHelper())->getInstance()->receiveWebhook($request);
    }

    public function getOperatorBundlePackage(Request $request)
    {
        $operator_id = $request->operator_id;

        try {
            $get_bundle_packages = (new MobileTopUpHelper())->getInstance()->getOperatorBundlePackage($operator_id);
        } catch (Exception $e) {
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return Response::error([$message], [], 500);
        }

        return response()->json([
            "message" => "Bundle Packages Fetch Successfully!",
            "data" => [
                "packages" => $get_bundle_packages,
            ]
        ]);
    }

    public function getVTPassBundleOperators()
    {
        return response()->json([
            "message" => "Bundle operators returned",
            "data" => [
                [
                    "name" => "MTN Data Nigeria",
                    "service_id" => "mtn-data"
                ],
                [
                    "name" => "Glo Data Nigeria",
                    "service_id" => "glo-data"
                ],
                [
                    "name" => "Glo SME Data Nigeria",
                    "service_id" => "glo-sme-data"
                ],
                [
                    "name" => "9Mobile Data Nigeria",
                    "service_id" => "etisalat-data"
                ],
                [
                    "name" => "Airtel Nigeria",
                    "service_id" => "airtel-data"
                ],
            ]
        ]);
    }

    public function getVTPassVariationCodes(Request $request)
    {
        $variation_codes = (new VTPass())->getDataBundleVariationCodes([
            "service_id" => $request->service_id
        ]);

        return response()->json([
            "message" => "Variation codes fetched successfully!",
            "data" => [
                "variation_codes" => $variation_codes,
            ]
        ]);
    }

    public function getAllOperators(Request $request)
    {
        try {
            $get_operators = (new MobileTopUpHelper())->getInstance()->getOperators([
                'size' => $request->size,
                'page' => $request->page,
                'dataOnly' => true
            ]);
        } catch (Exception $e) {
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return Response::error([$message], [], 500);
        }

        return response()->json([
            "message" => "Operators Fetch Successfully!",
            "data" => $get_operators
        ]);
    }
}
