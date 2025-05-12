<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Admin\SiteSections;
use App\Providers\Admin\BasicSettingsProvider;
use App\Constants\SiteSectionConst;
use Illuminate\Support\Facades\Validator;
use App\Models\Frontend\ContactRequest;
use App\Models\Admin\Language;
use Illuminate\Support\Facades\Session;
use Exception;

class ContactUsController extends Controller
{
    public function contact(BasicSettingsProvider $basic_settings)
    {
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $section_slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $contact_us       = SiteSections::getData($section_slug)->first();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        return view('frontend.pages.contact-us',compact('page_title','contact_us','footer'));
    }

    public function contactMessageSend(Request $request) {
        $request->merge(['phone' => null, 'reply' => null]);
        $validated = Validator::make($request->all(),[
            'name'      => "required|string|max:255",
            'email'     => "required|email|string|max:255",
            'message'   => "required|string|max:5000",
        ])->validate();

        try{
            ContactRequest::create($validated);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Failed to send message. Please Try again')]]);
        }

        return back()->with(['success' => [__('Message send successfully!')]]);
    }
    public function languageSwitch(Request $request) {
        $code = $request->target;
        $language = Language::where("code",$code)->first();
        if(!$language) {
            return back()->with(['error' => [__('Oops! Language Not Found!')]]);
        }
        Session::put('local',$code);
        Session::put('local_dir',$language->dir);

        return back()->with(['success' => ['Language Switch to ' . $language->name ]]);

    }
}
