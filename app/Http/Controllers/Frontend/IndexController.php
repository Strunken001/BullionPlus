<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Admin\Language;
use App\Models\Admin\UsefulLink;
use App\Models\Admin\SiteSections;
use App\Models\Frontend\Subscribe;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\InvestmentPlan;
use App\Models\Frontend\Announcement;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BasicSettingsProvider $basic_settings)
    {
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $section_slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $banner       = SiteSections::getData($section_slug)->first();
        $section_slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $services       = SiteSections::getData($section_slug)->first();
        $section_slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $how_it_works       = SiteSections::getData($section_slug)->first();
        $section_slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $securities       = SiteSections::getData($section_slug)->first();
        $section_slug = Str::slug(SiteSectionConst::DOWNLOAD_SECTION);
        $downloads       = SiteSections::getData($section_slug)->first();
        $section_slug = Str::slug(SiteSectionConst::STATISTIC_SECTION);
        $stats       = SiteSections::getData($section_slug)->first();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        return view('frontend.pages.index',compact('page_title','banner','services','how_it_works','securities','downloads','stats','footer'));
    }



    public function subscribe(Request $request) {
        $validator = Validator::make($request->all(),[
            'email'     => "required|string|email|max:255|unique:subscribes",
        ]);

        if($validator->fails()) return redirect('/#subscribe-form')->withErrors($validator)->withInput();

        $validated = $validator->validate();
        try{
            Subscribe::create([
                'email'         => $validated['email'],
                'created_at'    => now(),
            ]);
        }catch(Exception $e) {
            return redirect('/#subscribe-form')->with(['error' => [__('Failed to subscribe. Try again')]]);
        }

        return redirect(url()->previous() .'/#subscribe-form')->with(['success' => [__('Subscription successful!')]]);
    }

    public function usefulLink($slug) {
        $useful_link = UsefulLink::where("slug",$slug)->first();
        if(!$useful_link) abort(404);

        $basic_settings = BasicSettingsProvider::get();

        $app_local = get_default_language_code();
        $page_title = $useful_link->title?->language?->$app_local?->title ?? $basic_settings->site_name;

        // return view('frontend.pages.useful-link',compact('page_title','useful_link'));
    }


    public function languageSwitch(Request $request) {
        $code = $request->target;
        $language = Language::where("code",$code)->first();
        if(!$language) {
            return back()->with(['error' => [__('Oops! Language Not Found!')]]);
        }
        Session::put('local',$code);
        Session::put('local_dir',$language->dir);

        return back()->with(['success' => [__('Language Switch to ' . $language->name) ]]);
    }


}
