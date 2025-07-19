<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\KycController;
use App\Providers\Admin\BasicSettingsProvider;
use Pusher\PushNotifications\PushNotifications;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\AddMoneyController;
use App\Http\Controllers\User\ApiSettingsController;
use App\Http\Controllers\User\SecurityController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\DataBundleController;
use App\Http\Controllers\User\GiftCardController;
use App\Http\Controllers\User\MobileTopupController;
use App\Http\Controllers\User\PurchaseController;
use App\Http\Controllers\User\RechargeController;
use App\Http\Controllers\User\SupportTicketController;
use App\Http\Controllers\User\UtilityBillController;

Route::middleware('sms.verification.guard')->prefix("user")->name("user.")->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
        Route::get('recharge/history', 'rechargeHistory')->name('recharge.history');
        Route::get('/data/fetch', 'fetch')->name('data.fetch');
        Route::post('logout', 'logout')->name('logout')->withoutMiddleware(['sms.verification.guard', 'user.google.two.factor']);
    });

    Route::controller(ProfileController::class)->prefix("profile")->name("profile.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/password/change/view', 'passwordChange')->name('password');
        Route::put('password/update', 'passwordUpdate')->name('password.update');
        Route::put('update', 'update')->name('update');
        Route::get('delete', 'profileDelete')->name('delete');
    });

    Route::controller(SupportTicketController::class)->prefix("prefix")->name("support.ticket.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('conversation/{encrypt_id}', 'conversation')->name('conversation');
        Route::post('message/send', 'messageSend')->name('messaage.send');
    });

    Route::controller(SecurityController::class)->prefix("security")->name('security.')->group(function () {
        Route::get('google/2fa', 'google2FA')->name('google.2fa');
        Route::post('google/2fa/status/update', 'google2FAStatusUpdate')->name('google.2fa.status.update');
    });

    Route::controller(KycController::class)->prefix('kyc')->name('kyc.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('submit', 'store')->name('submit');
    });

    Route::controller(AddMoneyController::class)->prefix('add-money')->name('add.money.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('submit', 'submit')->name('submit');

        Route::get('success/response/{gateway}', 'success')->name('payment.success');
        Route::get("cancel/response/{gateway}", 'cancel')->name('payment.cancel');
        Route::post("callback/response/{gateway}", 'callback')->name('payment.callback')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);

        // POST Route For Unauthenticated Request
        Route::post('success/response/{gateway}', 'postSuccess')->name('payment.success')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor']);
        Route::post('cancel/response/{gateway}', 'postCancel')->name('payment.cancel')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor']);

        // redirect with HTML form route
        Route::get('redirect/form/{gateway}', 'redirectUsingHTMLForm')->name('payment.redirect.form')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor']);

        //redirect with Btn Pay
        Route::get('redirect/btn/checkout/{gateway}', 'redirectBtnPay')->name('payment.btn.pay')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor']);

        Route::get('manual/{token}', 'showManualForm')->name('manual.form');
        Route::post('manual/submit/{token}', 'manualSubmit')->name('manual.submit');

        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('crypto/address/{trx_id}', 'cryptoPaymentAddress')->name('crypto.address');
            Route::post('crypto/confirm/{trx_id}', 'cryptoPaymentConfirm')->name('crypto.confirm');
        });
    });

    Route::controller(RechargeController::class)->prefix('recharge')->name('recharge.')->group(function () {
        Route::post('recharge/preview', 'addMoneyPreview')->name('recharge.preview');
        Route::post('submit/amount', 'submitAmount')->name('submit.amount');
        Route::get('recharge/view', 'rechargeView')->name('recharge.view');
        Route::post('submit', 'submit')->name('submit');

        Route::get('success/response/{gateway}', 'success')->name('payment.success')->withoutMiddleware('sms.verification.guard');
        Route::get("cancel/response/{gateway}", 'cancel')->name('payment.cancel')->withoutMiddleware('sms.verification.guard');
        Route::post("callback/response/{gateway}", 'callback')->name('payment.callback')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);

        // POST Route For Unauthenticated Request
        Route::post('success/response/{gateway}', 'postSuccess')->name('payment.success')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor', 'sms.verification.guard']);
        Route::post('cancel/response/{gateway}', 'postCancel')->name('payment.cancel')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor', 'sms.verification.guard']);

        // redirect with HTML form route
        Route::get('redirect/form/{gateway}', 'redirectUsingHTMLForm')->name('payment.redirect.form')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor']);

        //redirect with Btn Pay
        Route::get('redirect/btn/checkout/{gateway}', 'redirectBtnPay')->name('payment.btn.pay')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor']);

        Route::get('manual/{token}', 'showManualForm')->name('manual.form');
        Route::post('manual/submit/{token}', 'manualSubmit')->name('manual.submit');
    });


    Route::controller(PurchaseController::class)->prefix('purchase')->name('purchase.')->group(function () {
        Route::get('purchase/history/view', 'purchaseView')->name('history');
    });

    // Giftcard User routes
    Route::controller(GiftCardController::class)->prefix('gift-card')->name('gift.card.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/list', 'giftCards')->name('list')->middleware('kyc.verification.guard');
        Route::get('details/{product_id}', 'details')->name('details')->middleware('kyc.verification.guard');
        Route::post('order', 'giftCardOrder')->name('order')->middleware('kyc.verification.guard');
        Route::get('search', 'giftSearch')->name('search')->middleware('kyc.verification.guard');
        Route::post('webhook', 'webhookInfo')->name('webhook')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);
        Route::post("balance", "balance")->name("wallet.balance");
        Route::get('all-products', 'getGiftcardProducts')->name('get.all.products')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);
    });

    //Mobile TopUp
    Route::controller(MobileTopupController::class)->prefix('mobile-topup')->name('mobile.topup.')->group(function () {
        Route::get('all-operators', 'getAllOperators')->name('get.all.operators')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);
        Route::get('get-operator', 'getOperatorsByCountry')->name('get.operator')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);
        //automatic method
        Route::prefix('automatic')->name('automatic.')->group(function () {
            Route::get('/', 'automaticTopUp')->name('index');
            Route::post('check-operator', 'checkOperator')->name('check.operator');
            Route::post('pay', 'payAutomatic')->name('pay')->middleware('kyc.verification.guard');
        });
    });

    //Bundle TopUp
    Route::controller(DataBundleController::class)->prefix('data-bundle')->name('data.bundle.')->group(function () {
        Route::get('get/operators/', 'getReloadlyOperators')->name('get.operators')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);
        Route::get('get/bundle/index', 'index')->name('index');
        Route::post('get/bundle/preview', 'preview')->name('preview');
        Route::post('get/bundle/buy', 'buyBundle')->name('buy');
        Route::post('bundle/webhook', 'receiveWebhook')->name('webhook')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);
        Route::get('get/bundle/packages', 'getOperatorBundlePackage')->name('get.packages');
        Route::get('get/bundle/operators/vtpass', 'getVTPassBundleOperators')->name('get.operators.vtpass');
        Route::get('get/bundle/packages/vtpass', 'getVTPassVariationCodes')->name('get.packages.vtpass');
        Route::get('get/operators/all', 'getAllOperators')->name('get.all.operators')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);
    });

    Route::controller(UtilityBillController::class)->prefix('utility-bill')->name('utility.bill.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('get/billers', 'getUtiityBiller')->name('get.billers')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);
        Route::post('preview', 'preview')->name('preview');
        Route::post('pay', 'payBill')->name('pay');
        // Route::get('history', 'history')->name('history');
        // Route::get('details/{trx_id}', 'details')->name('details');
    });

    Route::controller(ApiSettingsController::class)->prefix('api-settings')->name('api.settings.')->group(function () {
        Route::get('index', 'index')->name('index');
        Route::post('generate-keys', 'generateKeys')->name('generate.keys');
    });
});


// Route For Pusher Beams Auth
Route::get('user/pusher/beams-auth', function (Request $request) {
    if (Auth::check() == false) {
        return response(['Inconsistent request'], 401);
    }
    $userID = Auth::user()->id;

    $basic_settings = BasicSettingsProvider::get();
    if (!$basic_settings) {
        return response('Basic setting not found!', 404);
    }

    $notification_config = $basic_settings->push_notification_config;

    if (!$notification_config) {
        return response('Notification configuration not found!', 404);
    }

    $instance_id    = $notification_config->instance_id ?? null;
    $primary_key    = $notification_config->primary_key ?? null;
    if ($instance_id == null || $primary_key == null) {
        return response('Sorry! You have to configure first to send push notification.', 404);
    }
    $beamsClient = new PushNotifications(
        array(
            "instanceId" => $notification_config->instance_id,
            "secretKey" => $notification_config->primary_key,
        )
    );

    $get_full_host_path = remove_special_char(get_full_url_host(), "-");

    $publisherUserId = $get_full_host_path . "-user-" . $userID;
    try {
        $beamsToken = $beamsClient->generateToken($publisherUserId);
    } catch (Exception $e) {
        return response(['Server Error. Failed to generate beams token.'], 500);
    }

    return response()->json($beamsToken);
})->name('user.pusher.beams.auth');
