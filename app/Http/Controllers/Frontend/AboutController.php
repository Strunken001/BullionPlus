<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Admin\SiteSections;
use App\Providers\Admin\BasicSettingsProvider;
use App\Constants\SiteSectionConst;


use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function about(BasicSettingsProvider $basic_settings)
    {
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $section_slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $about_us       = SiteSections::getData($section_slug)->first();
        $section_slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $faq       = SiteSections::getData($section_slug)->first();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        return view('frontend.pages.about-us',compact('page_title','about_us','faq','footer'));
    }
}
