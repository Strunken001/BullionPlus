<?php

namespace App\Http\Controllers\User;

use App\Constants\GlobalConst;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AirtimeHelper;
use App\Http\Helpers\NotificationHelper;
use App\Http\Helpers\PushNotificationHelper;
use App\Http\Helpers\VTPass;
use App\Models\Admin\TransactionSetting;
use App\Models\TopupCategory;
use App\Models\Transaction;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Notifications\User\MobileTopup\TopupMail;
use App\Providers\Admin\BasicSettingsProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\ExchangeRate;
use App\Models\Admin\QuickRecharges;
use App\Models\Admin\SiteSections;
use App\Notifications\Admin\ActivityNotification;
use App\Notifications\User\MobileTopup\TopupAutomaticMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MobileTopupController extends Controller

{
    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    public function index()
    {
        $page_title = __("Mobile Topup");
        return view('user.sections.mobile-top.index', compact("page_title", "footer", "section_data"));
    }
    //start automatic
    public function automaticTopUp()
    {
        $page_title = __("Mobile Topup");
        $topupCharge = TransactionSetting::where('slug', 'mobile_topup')->where('status', 1)->first();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        $section_slug = Str::slug(SiteSectionConst::AIR_TIME_SECTION);
        $section_data = SiteSections::getData($section_slug)->first();
        $quick_buttons = QuickRecharges::where('key', 'quick-topup')->first();
        return view('user.page.mobile-top-up', compact("page_title", "topupCharge", "footer", "section_data", "quick_buttons"));
    }
    public function checkOperator(Request $request)
    {
        $phone = $request->mobile_code . $request->phone;
        $iso = $request->iso;
        $operator = (new AirtimeHelper())->autoDetectOperator($phone, $iso);
        if ($operator['status'] === false) {
            $data = [
                'status' => false,
                'message' => $operator['message'] ?? "",
                'data' => [],
                'from' => "error",
            ];
        } else {
            $data = [
                'status' => true,
                'message' => 'Successfully Get Operator',
                'data' => $operator,
                'from' => "success",
            ];
        }
        return response($data);
    }
    public function payAutomatic(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'operator_id' => 'required',
            'phone_code' => 'required',
            'country_code' => 'required',
            'mobile_number' => 'required|min:10|max:15',
            'amount' => 'required|numeric|gt:0',
        ])->validate();

        $user = userGuard()['user'];
        $sender_phone = $user->full_mobile ?? "";
        $sender_country_name = @$user->address->country;
        $foundItem = '';
        foreach (get_all_countries(GlobalConst::USER) ?? [] as $item) {
            if ($item->name === $sender_country_name) {
                $foundItem = $item;
            }
        }
        $sender_country_iso = $foundItem->iso2;

        $phone = remove_speacial_char($validated['phone_code']) . $validated['mobile_number'];
        $operator = (new AirtimeHelper())->autoDetectOperator($phone, $validated['country_code']);
        if ($operator['status'] === false) {
            return back()->with(['error' => [__($operator['message'] ?? "")]]);
        }
        $sender_wallet = UserWallet::where('user_id', $user->id)->first();
        if (!$sender_wallet) {
            return back()->with(['error' => [__('User Wallet not found')]]);
        }
        $topupCharge = TransactionSetting::where('slug', 'mobile_topup')->where('status', 1)->first();
        $charges = $this->topupChargeAutomatic($validated['amount'], $operator, $sender_wallet, $topupCharge);
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
                return back()->with(['error' => [__("Please follow the transaction limit")]]);
            }
        }

        if ($charges['payable'] > $sender_wallet->balance) {
            return back()->with(['error' => [__("Sorry, insufficient balance")]]);
        }
        //topup api
        $topUpData = [];

        if ($request->country_code === "NG") {
            $operator = json_decode($request->operator, true);

            $network_provider = $operator['name'] ?? null;

            $vtpass_service_id = explode(" ", $network_provider)[0];

            $service_id = $vtpass_service_id == "9Mobile" ? "etisalat" : strtolower($vtpass_service_id);
            $topUpData = [
                "service_id" => $service_id,
                "amount" => $validated['amount'],
                "phone" => $validated['mobile_number'],
                "customIdentifier" => Str::uuid() . "|" . "AIRTIME",
            ];

            $topUpData = (new VTPass())->mobileTopUp($topUpData);
        } else {
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
            return back()->with(['error' => [$topUpData['message']]]);
        }

        try {
            $trx_id = 'MP' . getTrxNum();
            $sender = $this->insertTransaction($trx_id, $sender_wallet, $charges, $operator, $phone, $topUpData);
            $this->insertAutomaticCharges($sender, $charges, $sender_wallet, $phone, $trx_id);

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
            return redirect()->route("user.mobile.topup.automatic.index")->with(['success' => [__('Mobile topup request successful')]]);
        } catch (Exception $e) {
            return back()->with(['error' => [__("Something went wrong! Please try again.")]]);
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
                'fixed_charge'                   => $charges['fixed_charge'],
                'total_charge'                  => $charges['total_charge'],
                'request_currency'              => $charges['destination_currency'],
                'total_payable'                 => $charges['payable'],
                'available_balance'             => $afterCharge,
                'remark'                        => ucwords(remove_speacial_char(PaymentGatewayConst::MOBILETOPUP, " ")) . " Request Successful",
                'details'                       => json_encode($details),
                'callback_ref'                  => $topUpData['customIdentifier'],
                'status'                        => $status,
                'created_at'                    => now(),
            ]);
            $this->updateSenderWalletBalance($authWallet, $afterCharge);

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

            try {
                // Push Notification
                (new PushNotificationHelper())->prepare([$sender_wallet->user->id], [
                    'title' => $notification_content['title'],
                    'desc'  => $notification_content['message'],
                    'user_type' => 'user',
                ])->send();
                sendSms($sender_wallet->user, 'MOBILE_TOPUP', '', ['amount' => $charges['sender_amount'], 'request_currency' => $charges['destination_currency'], 'mobile_number' => $phone, 'trx' => $trx_id]);
            } catch (Exception $e) {
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception(__("Something went wrong! Please try again."));
        }
    }
    public function topupChargeAutomatic($sender_amount, $operator, $sender_wallet, $charges)
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
        $data['percent_charge']                     = (($sender_amount * $exchange_rate) / 100) * $charges->percent_charge ?? 0;
        $data['fixed_charge']                       = $sender_wallet->currency->rate * $charges->fixed_charge ?? 0;
        $data['total_charge']                       = $data['percent_charge'] + $data['fixed_charge'];
        $data['sender_wallet_balance']              = $sender_wallet->balance;
        $data['payable']                            = $data['conversion_amount'] + $data['total_charge'];

        return $data;
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
            'email_content' => __("trx_id") . " : " . $trx_id . "<br>" . __("Mobile Number") . " : " . $phone . "<br>" . __("Operator Name") . " : " . $operator['name'] . "<br>" . __("request Amount") . " : " . get_amount($charges['sender_amount'], $charges['destination_currency']) . "<br>" . __("Exchange Rate") . " : " . $exchange_rate . "<br>" . __("Fees & Charges") . " : " . get_amount($charges['total_charge'], $charges['sender_currency']) . "<br>" . __("Total Payable Amount") . " : " . get_amount($charges['payable'], $charges['sender_currency']) . "<br>" . __("Status") . " : " . __($status),

            //push notification
            'push_title' => __("Mobile topup request successful") . " (" . userGuard()['type'] . ")",
            'push_content' => __('trx_id') . " : " . $trx_id . "," . __("request Amount") . " : " . get_amount($charges['sender_amount'], $charges['destination_currency']) . "," . __("Operator Name") . " : " . $operator['name'] . "," . __("Mobile Number") . " : " . $phone,

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

    public function getAllOperators(Request $request)
    {
        $operators = (new AirtimeHelper())->getOperators([
            'page' => $request->page,
            'size' => $request->size,
        ]);

        return response()->json([
            "message" => "Operator Fetch Successfully!",
            'data' => $operators
        ]);
    }

    public function getOperatorsByCountry(Request $request)
    {
        $operator = (new AirtimeHelper())->getOperatorsByCountry($request->iso2);

        return response()->json([
            "message" => "Operator Fetch Successfully!",
            'data' => $operator
        ]);
    }
}
