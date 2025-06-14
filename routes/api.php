<?php

use App\Http\Controllers\Api\V1\User\DataBundleController;
use App\Http\Controllers\Api\V1\User\GiftCardController;
use App\Http\Controllers\Api\V1\User\MobileTopupController;
use App\Http\Controllers\Api\V1\User\TransactionController;
use App\Http\Controllers\Api\V1\User\UtilityBillController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Transaction
Route::controller(TransactionController::class)->prefix("transaction")->group(function () {
    Route::get("log", "log");
});

//gift card
Route::controller(GiftCardController::class)->prefix('gift-card')->group(function () {
    Route::get('/', 'index');
    Route::get('all', 'allGiftCard');
    Route::get('search', 'searchGiftCard');
    Route::get('details', 'giftCardDetails');
    Route::post('order', 'orderPlace')->middleware('api.kyc');
});


Route::controller(MobileTopupController::class)->prefix('mobile-topup')->group(function () {
    //automatic methodm
    Route::prefix('automatic')->group(function () {
        Route::post('check-operator', 'checkOperator');
        Route::post('pay', 'payAutomatic')->middleware('api.kyc');
    });
});

//Bundle TopUp
Route::controller(DataBundleController::class)->prefix('data-bundles')->name('data.bundle.')->group(function () {
    Route::get('', 'getDataBundles');
    Route::get('plans', 'getDataBundlePlans');
    Route::post('get/bundle/charges', 'getCharges');
    Route::post('buy', 'buyBundle')->name('buy');
    // Route::post('bundle/webhook', 'receiveWebhook')->name('webhook')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);
});

Route::controller(UtilityBillController::class)->prefix('utility-bills')->name('utility.bill.')->group(function () {
    Route::get('', 'getUtiityBiller');
    Route::post("pay", "payBill");
});
