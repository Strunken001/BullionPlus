<?php

use App\Http\Controllers\GlobalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\File;

Route::controller(GlobalController::class)->prefix('global')->name('global.')->group(function(){
    Route::post('get-states','getStates')->name('country.states');
    Route::post('get-cities','getCities')->name('country.cities');
    Route::post('get-countries','getCountries')->name('countries');
    Route::post('get-timezones','getTimezones')->name('timezones');
    Route::get('receiver/wallet/currency','receiverWallet')->name('receiver.wallet.currency');
    //webhook(Airtime(Reloadly))
    Route::post('mobile-topup/webhook', 'webhookInfo')->name('mobile.topup.webhook')->withoutMiddleware(['web','auth','verification.guard','user.google.two.factor']);

    Route::get('usefull/page/{slug}','userfullPage')->name('usefull.page');

    Route::post('set-cookie','setCookie')->name('set.cookie');
});

// FileHolder Routes
Route::post('fileholder-upload',[FileController::class,'storeFile'])->name('fileholder.upload');
Route::post('fileholder-remove',[FileController::class,'removeFile'])->name('fileholder.remove');

Route::get("file/download/{path_source}/{name}",function($path_source,$file_name) {
    $file_link = get_files_path($path_source) . "/" . $file_name;
    if(File::exists($file_link)) return response()->download($file_link);
    return back()->with(['error' => ['File doesn\'t exists']]);
})->name('file.download');
