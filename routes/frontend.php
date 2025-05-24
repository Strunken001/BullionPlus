<?php

use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ContactUsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Frontend\PricingController;
use App\Http\Controllers\Frontend\ServicesController;

Route::name('frontend.')->group(function () {
    Route::controller(IndexController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('check_page:home');
        Route::post("subscribe", "subscribe")->name("subscribe");
        Route::get('link/{slug}', 'usefulLink')->name('useful.links');
        Route::post('languages/switch', 'languageSwitch')->name('languages.switch');
    });
});
Route::name('frontend.')->group(function () {
    Route::controller(AboutController::class)->group(function () {
        Route::get('/about', 'about')->name('about')->middleware('check_page:about');
    });
});
Route::name('frontend.')->group(function () {
    Route::controller(ServicesController::class)->group(function () {
        Route::get('/services', 'services')->name('services')->middleware('check_page:services');
    });
});
Route::name('frontend.')->group(function () {
    Route::controller(BlogController::class)->group(function () {
        Route::get('/blog', 'blog')->name('blog')->middleware('check_page:blog');
        Route::get('/blog/view/{id}', 'blogSingle')->name('blog.view')->middleware('check_page:blog');
        Route::get('/blog/category/view/{category}', 'blogCategory')->name('blog.category.view')->middleware('check_page:blog');
    });
});
Route::name('frontend.')->group(function () {
    Route::controller(ContactUsController::class)->group(function () {
        Route::get('/contact', 'contact')->name('contact')->middleware('check_page:contact');
        Route::post("contact/message/send", "contactMessageSend")->name("contact.message.send");
    });
});

Route::name('frontend.')->group(function () {
    Route::controller(PricingController::class)->group(function () {
        Route::get("/pricing", "pricing")->name('pricing')->middleware('check_page:pricing');
    });
});
