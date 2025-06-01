<?php

namespace App\Http\Controllers\User;

use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Constants\UtilityBillTypeConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\NotificationHelper;
use App\Http\Helpers\PushNotificationHelper;
use App\Http\Helpers\UtilityPaymentHelper;
use App\Http\Helpers\Response;
use App\Http\Helpers\VTPass;
use App\Models\UserNotification;
use App\Models\VTPassAPIDiscount;
use App\Notifications\Admin\ActivityNotification;
use App\Notifications\UtilityPaymentMail;
use App\Providers\Admin\BasicSettingsProvider;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UtilityBillController extends Controller
{
    private $basic_settings;

    public function __construct()
    {
        $this->basic_settings =  BasicSettingsProvider::get();
    }

    public function index()
    {
        $page_title = "Utility Bill";
        return view('user.page.utility-bill', compact('page_title'));
    }

    public function getUtiityBiller(Request $request)
    {
        try {
            $billers = null;

            if ($request->iso2 === "NG") {
                $billers = VTPassAPIDiscount::where("type", "utility_bill")->get();
            } else {
                $billers = (new UtilityPaymentHelper())->getInstance()->getUtilityBillers([
                    'countryISOCode' => $request->iso2,
                    'page' => $request->page ?? 1,
                    'size' => $request->size ?? 10,
                ]);
            }
        } catch (Exception $e) {
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return Response::error([$message], [], 500);
        }

        return response()->json([
            'status' => true,
            'message' => __('Utility biller list'),
            'data' => $billers,
        ]);
    }

    public function payBill(Request $request)
    {
        try {
            $charges = json_decode($request->charges);

            $utility_bill_transaction = null;
            $account_number = null;

            if ($charges->currency_code === "NGN") {
                $service_id = strtolower(explode(" ", $charges->name)[0]) . "-electric";
                $variation_code = strtolower($charges->service_type);
                $amount = $request->amount;
                $account_number = $request->account_number;
                $phone = auth()->user()->full_mobile;

                $payment = (new VTPass())->utilityPayment([
                    'service_id' => $service_id,
                    'variation_code' => $variation_code,
                    'amount' => $amount,
                    'account_number' => $account_number,
                    'phone' => $phone
                ]);

                $tx_ref = $payment['requestId'];

                $utility_bill_transaction = (new VTPass())->verifyUtilityPaymentTransaction($tx_ref);
            } else {
                $request['tx_ref'] = generate_unique_string('transactions', 'trx_id', 16);
                $request['useLocalAmount'] = true;

                $payment = (new UtilityPaymentHelper())->getInstance()->payBill($request->all());
                $tx_ref = $payment['referenceId'];

                sleep(20);

                $utility_bill_transaction = (new UtilityPaymentHelper())->getInstance()->getUtilityBillTransaction($tx_ref);

                $account_number = $utility_bill_transaction['transaction']['billDetails']['subscriberDetails']['accountNumber'];
            }

            if (isset($utility_bill_transaction) && isset($utility_bill_transaction['transaction']['status']) && $utility_bill_transaction['transaction']['status'] !== "SUCCESSFUL") {
                return redirect()->route("user.utility.bill.index")->with(['error' => [__('Unable to complete transaction at the moment. Please try again later')]]);
            }

            $this->insertTransaction($tx_ref, auth()->user()->wallets, $charges, $utility_bill_transaction, $account_number);
        } catch (Exception $e) {
            Log::error("Utility Bill Payment Error: " . $e->getMessage());
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            // return Response::error([$message], [], 500);
            return redirect()->route("user.utility.bill.index")->with(['error' => [__($message)]]);
        }


        return redirect()->route("user.dashboard")->with(['success' => [__('Payment successful!')]]);
    }

    public function preview(Request $request)
    {
        $info = $request->all();
        try {
            $charges = (new UtilityPaymentHelper())->getInstance()->getUtilityBillCharge($info);

            if ($charges['total_payable'] < $charges['min_limit_calc'] || $charges['total_payable'] > $charges['max_limit_calc']) {
                throw new Exception("Please follow the transaction limit!");
            }
        } catch (Exception $e) {
            $message = app()->environment() == "production" ? __("Oops! Something went wrong! Please try again") : $e->getMessage();

            return Response::error([$message], [], 500);
        }
        return view('user.page.utility-preview', compact('info', 'charges'));
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
            $id = DB::table("transactions")->insertGetId([
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
                $this->insertAutomaticCharges($id, $charges, $sender_wallet, $account_number, $trx_id);
                $user = auth()->user();

                // dd($charges);

                if ($this->basic_settings->email_notification == true) {
                    //send notifications
                    $notifyData = [
                        'trx_id'            => $trx_id,
                        'operator_name'     => $operator['name'] ?? '',
                        'account_number'    => $account_number,
                        'request_amount'    => get_amount($charges->amount, $charges->bundle_currency),
                        'exchange_rate'     => get_amount(1, $charges->bundle_currency) . " = " . get_amount($charges->rate, $charges->wallet_currency_code, 4),
                        'charges'           => get_amount($charges->total_charge_calc, $charges->wallet_currency_code),
                        'payable'           => get_amount($charges->total_payable, $charges->wallet_currency_code),
                        'current_balance'   => get_amount($sender_wallet->balance, $charges->wallet_currency_code),
                        'token'             => $utility_bill_transaction['transaction']['billDetails']['pinDetails']['token'] ?? "--",
                        'status'            => __("Successful"),
                    ];
                    try {
                        $user->notify(new UtilityPaymentMail($user, (object)$notifyData));
                    } catch (Exception $e) {
                    }
                }
                //admin notification
                $this->adminNotificationAutomatic($trx_id, $charges, $user, $account_number, $utility_bill_transaction);
            } catch (Exception $e) {
                Log::error("Utility Bill Payment Error: " . $e->getMessage());
                return back()->with(['error' => [__("Something went wrong! Please try again.")]]);
            }

            DB::commit();
        } catch (Exception $e) {
            Log::error("Utility Bill Payment Error: " . $e->getMessage());
            DB::rollBack();
            throw new Exception(__("Something went wrong! Please try again."));
        }
        return $id;
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
}
