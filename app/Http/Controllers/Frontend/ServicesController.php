<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Admin\SiteSections;
use App\Providers\Admin\BasicSettingsProvider;
use App\Constants\SiteSectionConst;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function services(BasicSettingsProvider $basic_settings)
    {
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $section_slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $services       = SiteSections::getData($section_slug)->first();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        return view('frontend.pages.services',compact('page_title','services','footer'));
    }
}
