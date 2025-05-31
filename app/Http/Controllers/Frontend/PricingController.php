<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\BasicSettings;
use App\Models\Admin\SiteSections;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PricingController extends Controller
{
    public function pricing(BasicSettingsProvider $basic_settings)
    {
        $page_title = $basic_settings->get()->site_name . " | " . $basic_settings->get()->site_title;
        $section_slug = Str::slug(SiteSectionConst::PRICING_SECTION);
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        $api_discount_percentage = BasicSettings::first()->api_discount_percentage;
        return view('frontend.pages.pricing', compact('page_title', 'footer', 'api_discount_percentage'));
    }
}
