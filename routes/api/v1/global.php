<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Providers\Admin\BasicSettingsProvider;
use Pusher\PushNotifications\PushNotifications;
use App\Http\Controllers\Api\V1\User\SettingController;
use App\Http\Controllers\GlobalController;

// Settings
Route::controller(SettingController::class)->prefix("settings")->group(function () {
    Route::get("basic-settings", "basicSettings");
    Route::get("splash-screen", "splashScreen");
    Route::get("onboard-screens", "onboardScreens");
    Route::get("languages", "getLanguages")->withoutMiddleware(['system.maintenance.api']);
    Route::get("country", "getCountries");
});

Route::controller(GlobalController::class)->prefix('global')->name('global.')->group(function () {
    Route::post('get-states', 'getStates')->name('country.states');
    Route::post('get-cities', 'getCities')->name('country.cities');
    Route::post('get-countries', 'getCountries')->name('countries');
    Route::post('get-timezones', 'getTimezones')->name('timezones');
    Route::get('receiver/wallet/currency', 'receiverWallet')->name('receiver.wallet.currency');
    //webhook(Airtime(Reloadly))
    Route::post('mobile-topup/webhook', 'webhookInfo')->name('mobile.topup.webhook')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);

    Route::get('usefull/page/{slug}', 'userfullPage')->name('usefull.page');

    Route::post('set-cookie', 'setCookie')->name('set.cookie');
});
