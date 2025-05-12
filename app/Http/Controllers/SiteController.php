<?php

namespace App\Http\Controllers;

use App\Constants\PaymentGatewayConst;
use App\Http\Helpers\Api\Helpers;
use App\Http\Helpers\NotificationHelper;
use App\Http\Helpers\PayLinkPaymentGateway;
use App\Http\Helpers\PaymentGateway;
use App\Http\Helpers\PaymentGatewayApi;
use App\Models\Admin\AppSettings;
use App\Models\Admin\BasicSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use App\Models\Admin\Language;
use App\Models\Admin\SetupPage;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Contact;
use App\Models\Newsletter;
use App\Models\PaymentLink;
use App\Models\TemporaryData;
use App\Notifications\Admin\ActivityNotification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    // public function changeLanguage($lang = null)
    // {
    //     $language = Language::where('code', $lang)->first();
    //     session()->put('local', $lang);
    //     return redirect()->back();
    // }
}
