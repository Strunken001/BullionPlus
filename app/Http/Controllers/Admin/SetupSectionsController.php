<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\Admin\Language;
use App\Constants\LanguageConst;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Frontend\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Frontend\AnnouncementCategory;
use App\Models\Frontend\Blogs;
use App\Models\Frontend\BlogsCategory;

class SetupSectionsController extends Controller
{
    protected $languages;

    public function __construct()
    {
        $this->languages = Language::get();
    }

    /**
     * Register Sections with their slug
     * @param string $slug
     * @param string $type
     * @return string
     */
    public function section($slug, $type)
    {
        $sections = [
            'auth-section'    => [
                'view'      => "authView",
                'update'    => "authUpdate",
            ],
            'banner'    => [
                'view'          => "bannerView",
                'itemStore'     => "bannerStore",
                'itemUpdate'    => "bannerUpdate",
                'itemDelete'    => "bannerDelete",
            ],
            'services'  => [
                'view'          => "servicesView",
                'itemStore'     => "servicesItemStore",
                'itemUpdate'    => "servicesItemUpdate",
                'itemDelete'    => "servicesItemDelete",
            ],
            'how-it-work' => [
                'view'          => "howItWorkView",
                'update'        => "howItWorkUpdate",
                'itemStore'     => "howItworkItemStore",
                'itemUpdate'    => "howItworkItemUpdate",
                'itemDelete'    => "howItworkItemDelete",
            ],
            'security' => [
                'view'          => "securityView",
                'update'        => "securityUpdate",
                'itemStore'     => "securityItemStore",
                'itemUpdate'    => "securityItemUpdate",
                'itemDelete'    => "securityItemDelete",
            ],
            'download-app' => [
                'view'          => "downloadView",
                'update'        => "downloadUpdate",
                'itemStore'     => "downloadItemStore",
                'itemUpdate'    => "downloadItemUpdate",
                'itemDelete'    => "downloadItemDelete",
            ],
            'statistic' => [
                'view'          => "statisticView",
                'itemStore'     => "statisticItemStore",
                'itemUpdate'    => "statisticItemUpdate",
                'itemDelete'    => "statisticItemDelete",
            ],
            'brand'    => [
                'view'      => "brandView",
                'itemStore'     => "brandItemStore",
                'itemDelete'    => "brandItemDelete",
            ],
            'about-us'  => [
                'view'          => "aboutUsView",
                'update'        => "aboutUsUpdate",
            ],
            'faq'  => [
                'view'          => "faqPageView",
                'update'        => "faqPageUpdate",
                'itemStore'     => "faqPageItemStore",
                'itemUpdate'    => "faqPageItemUpdate",
                'itemDelete'    => "faqPageItemDelete",
            ],
            'service-page'  => [
                'view'          => "servicePageView",
                'update'        => "servicePageUpdate",
                'itemStore'     => "servicePageItemStore",
                'itemUpdate'    => "servicePageItemUpdate",
                'itemDelete'    => "servicePageItemDelete",
            ],
            'gift-card'  => [
                'view'          => "giftCardView",
                'update'        => "giftCardUpdate",
            ],
            'air-time'  => [
                'view'          => "airTimeView",
                'update'        => "airTimeUpdate",
            ],
            'blog'  => [
                'view'      => "blogView",
                'update'    => "blogUpdate",
            ],
            'feature'  => [
                'view'      => "featureView",
                'update'    => "featureUpdate",
            ],
            'clients-feedback' => [
                'view'          => "clientsFeedbackView",
                'update'        => "clientsFeedbackUpdate",
                'itemStore'     => "clientsFeedbackItemStore",
                'itemUpdate'    => "clientsFeedbackItemUpdate",
                'itemDelete'    => "clientsFeedbackItemDelete",
            ],
            'announcement' => [
                'view'          => "announcementView",
                'update'        => "announcementUpdate",
            ],
            'about-page'  => [
                'view'          => "aboutPageView",
                'update'        => "aboutPageUpdate",
                'itemStore'     => "aboutPageItemStore",
                'itemUpdate'    => "aboutPageItemUpdate",
                'itemDelete'    => "aboutPageItemDelete",
            ],
            'contact-us' => [
                'view'          => "contactUsView",
                'update'        => "contactUsUpdate",
            ],
            'footer' => [
                'view'          => "footerView",
                'update'        => "footerUpdate",
            ]
        ];

        if (!array_key_exists($slug, $sections)) abort(404);
        if (!isset($sections[$slug][$type])) abort(404);
        $next_step = $sections[$slug][$type];
        return $next_step;
    }

    /**
     * Method for getting specific step based on incoming request
     * @param string $slug
     * @return method
     */
    public function sectionView($slug)
    {
        $section = $this->section($slug, 'view');

        return $this->$section($slug);
    }

    /**
     * Method for distribute store method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemStore(Request $request, $slug)
    {
        $section = $this->section($slug, 'itemStore');
        return $this->$section($request, $slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemUpdate(Request $request, $slug)
    {
        $section = $this->section($slug, 'itemUpdate');
        return $this->$section($request, $slug);
    }

    /**
     * Method for distribute delete method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemDelete(Request $request, $slug)
    {
        $section = $this->section($slug, 'itemDelete');
        return $this->$section($request, $slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionUpdate(Request $request, $slug)
    {
        $section = $this->section($slug, 'update');
        return $this->$section($request, $slug);
    }


    /**
     * Displays the view for the Auth Section.
     *
     * @param string $slug The slug for the Auth Section.
     * @return \Illuminate\View\View The view for the Auth Section.
     */
    public function authView($slug)
    {
        $page_title = __("Auth Section");
        $section_slug = Str::slug(SiteSectionConst::AUTH_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.auth-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }


    /**
     * Updates the Auth Section with the provided request data.
     *
     * @param \Illuminate\Http\Request $request The request containing the updated data.
     * @param string $slug The slug for the Auth Section.
     * @return \Illuminate\Http\RedirectResponse A redirect response with a success or error message.
     */
    public function authUpdate(Request $request,$slug) {
        $basic_field_name = [
            'register_text' => "required|string",
            'forget_text' => "required|string",
        ];
        $slug = Str::slug(SiteSectionConst::AUTH_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;
        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__("Something went wrong! Please try again.")]]);
        }
        return back()->with(['success' => [__("Section updated successfully!")]]);
    }

    /**
     * Method for show banner section page
     * @param string $slug
     * @return view
     */
    public function bannerView($slug)
    {
        $page_title = __("Banner Section");
        $section_slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;
        // dd($data);
        return view('admin.sections.setup-sections.banner-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }


    /**
     * Method for store banner section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function bannerStore(Request $request, $slug)
    {
        $basic_field_name = [
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string|max:500",
            'button_name' => "required|string|max:50",
        ];

        $validator = Validator::make($request->all(), [
            'image'     => "required|mimes:png,jpg,svg,webp,jpeg",
            'button_link' => "required|string|max:255",
        ]);
        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'bnr-add');
        $validated = $validator->validate();
        $language_wise_data = $this->contentValidate($request, $basic_field_name, "bnr-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['image'] = $validated['image'];
        $section_data['items'][$unique_id]['button_link'] = $validated['button_link'];

        if ($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image'] = $this->imageValidate($request, "image", $section->value?->items?->image ?? null);
        }

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update banner item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function bannerUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'heading_edit' => "required|string|max:100",
            'sub_heading_edit' => "required|string|max:500",
            'button_name_edit' => "required|string|max:50",
        ];

        $validator = Validator::make($request->all(), [
            'button_link_edit' => "required|string|max:255",
        ]);

        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'banner-edit');

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "banner-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);


        $language_wise_data = $this->contentValidate($request, $basic_field_name, "banner-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);


        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['id'] = $request->target;
        $section_values['items'][$request->target]['button_link'] =  $request->button_link_edit;

        if ($request->hasFile("image_edit")) {
            $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
            delete_file($image_link);
            $section_values['items'][$request->target]['image'] = $this->imageValidate($request, "image_edit", $section_values['items'][$request->target]['image'] ?? null);
        }

        $update_data['value']   = $section_values;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for delete testimonial item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function bannerDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
            unset($section_values['items'][$request->target]);
            delete_file($image_link);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }

    /**
     * Method for show How It Work section page
     * @param string $slug
     * @return view
     */
    public function howItWorkView($slug)
    {
        $page_title = __("How It Work Section");
        $section_slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.how-it-work-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update How It Work section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function howItWorkUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'heading'       => "required|string",
            'sub_heading'   => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $section = SiteSections::where("key", $slug)->first();
        $data['image'] = $section->value->image ?? null;
        if ($request->hasFile("image")) {
            if ($data['image']) {
                $image_link = get_files_path('site-section') . '/' . $data['image'];
                delete_file($image_link);
            }
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['value']['items']  = $section->value->items ?? [];
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    /**
     * Method for store how it work item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function howItworkItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'description'   => "required|string|max:500",
        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "how-it-works-item-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update how it work item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function howItworkItemUpdate(Request $request, $slug)
    {
        $request->validate([
            'target'        => "required|string",
        ]);

        $basic_field_name = [
            'description_edit'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "how-it-works-item-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete how it work item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function howItworkItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }


    /**
     * Method for show Security section page
     * @param string $slug
     * @return view
     */
    public function securityView($slug)
    {
        $page_title = __("Security Section");
        $section_slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.security-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update Security section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securityUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'heading'       => "required|string|max:100",
            'sub_heading'   => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $section = SiteSections::where("key", $slug)->first();
        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['value']['items'] = $section->value->items ?? [];
        $update_data['key']    = $slug;
        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    /**
     * Method for store Security item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securityItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'title'       => "required|string|max:100",
            'highlighted_title'  => "required|string|max:100",
            'description'   => "required|string|max:500",
        ];

        $validator = Validator::make($request->all(), [
            'icon'      => "required|string|max:255",
        ]);
        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'security-item-add');
        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "security-item-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update Security item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securityItemUpdate(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'target'        => "required|string",
            'icon_edit'      => "required|string|max:255",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'highlighted_title_edit'     => "required|string|max:255",
            'description_edit'   => "required|string|max:500",
        ];

        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'security-item-edit');
        $validated = $validator->validate();

        $slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "security-item-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete Security item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securityItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }


    /**
     * Method for show Download section page
     * @param string $slug
     * @return view
     */
    public function downloadView($slug)
    {
        $page_title = __("Download Section");
        $section_slug = Str::slug(SiteSectionConst::DOWNLOAD_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.download-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update Download section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function DownloadUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'heading'       => "required|string",
            'sub_heading'   => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::DOWNLOAD_SECTION);
        $section = SiteSections::where("key", $slug)->first();
        $data['image'] = $section->value->image ?? null;
        if ($request->hasFile("image")) {
            if ($data['image']) {
                $image_link = get_files_path('site-section') . '/' . $data['image'];
                delete_file($image_link);
            }
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['value']['items'] = $section->value->items ?? [];
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    /**
     * Method for store Download item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function downloadItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'title' => "required|string|max:100",
        ];

        $validator = Validator::make($request->all(), [
            'link'  => "required|string|max:100",
            'icon'      => "required|string|max:255",
        ]);
        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'download-item-add');
        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "download-item-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::DOWNLOAD_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['link'] = $validated['link'];
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update Download item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function downloadItemUpdate(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'target'        => "required|string",
            'icon_edit'      => "required|string|max:255",
            'link_edit'     => "required|string|max:255",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
        ];

        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'download-item-edit');
        $validated = $validator->validate();

        $slug = Str::slug(SiteSectionConst::DOWNLOAD_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "download-item-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['link']    = $request->link_edit;
        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete Delete item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function downloadItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::DOWNLOAD_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }

    /**
     * Method for show statistic section page
     * @param string $slug
     * @return view
     */
    public function statisticView($slug)
    {
        $page_title = __("Statistic Section");
        $section_slug = Str::slug(SiteSectionConst::STATISTIC_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.statistic-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for store statistic item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function statisticItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'title'         => "required|string|max:255",
        ];

        $validator = Validator::make($request->all(), [
            'amount'         => "required|numeric",
            'icon'      => "required|string|max:255",
        ]);
        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'stat-item-add');
        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "stat-item-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::STATISTIC_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];
        $section_data['items'][$unique_id]['amount'] = $validated['amount'];


        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update statistic item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function statisticItemUpdate(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'target'        => "required|string",
            'icon_edit'      => "required|string|max:255",
            'amount_edit'         => "required|numeric",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
        ];

        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'stat-item-edit');

        $slug = Str::slug(SiteSectionConst::STATISTIC_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "stat-item-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon']    = $request->icon_edit;
        $section_values['items'][$request->target]['amount']    = $request->amount_edit;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete statistic item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function statisticItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::STATISTIC_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }



    /**
     * Method for show FAQ section page
     * @param string $slug
     * @return view
     */
    public function faqPageView($slug)
    {
        $page_title = __("FAQ Section");
        $section_slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.faq-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update FAQ section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function faqPageUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'heading'       => "required|string",
            'sub_heading'   => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::where("key", $slug)->first();
        $data['image'] = $section->value->image ?? null;
        if ($request->hasFile("image")) {
            if ($data['image']) {
                $image_link = get_files_path('site-section') . '/' . $data['image'];
                delete_file($image_link);
            }
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['value']['items'] = $section->value->items ?? [];
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    /**
     * Method for store FAQ item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function faqPageItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'question' => "required|string",
            'answer'  => "required|string",
        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "faq-item-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update FAQ item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function faqPageItemUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'question_edit'     => "required|string",
            'answer_edit'     => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "faq-item-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete FAQ item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function faqPageItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }

    /**
     * Method for show Service page section page
     * @param string $slug
     * @return view
     */
    public function servicePageView($slug)
    {
        $page_title = __("Service Page Section");
        $section_slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.service-page-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update Service page section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicePageUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'heading'       => "required|string",
            'sub_heading'   => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $section = SiteSections::where("key", $slug)->first();
        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['value']['items'] = $section->value->items ?? [];
        $update_data['key']    = $slug;
        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    /**
     * Method for store Service page item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicePageItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'title'       => "required|string|max:100",
            'description'   => "required|string|max:500",
        ];

        $validator = Validator::make($request->all(), [
            'link'   => "required|string",
            'image'     => "required|mimes:png,jpg,svg,webp,jpeg|max:10240",
        ]);
        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'service-page-item-add');
        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "service-page-item-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['link'] = $request->link;
        if ($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image'] = $this->imageValidate($request, "image", $section->value?->items?->image ?? null);
        }

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update Service page item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicePageItemUpdate(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'target'        => "required|string",
            'link_edit'   => "required|string",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'description_edit'   => "required|string|max:500",
        ];

        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'service-page-item-edit');
        $validated = $validator->validate();

        $slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "service-page-item-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        if ($request->hasFile("image_edit")) {
            $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
            delete_file($image_link);
            $section_values['items'][$request->target]['image'] = $this->imageValidate($request, "image_edit", $section_values['items'][$request->target]['image'] ?? null);
        }
        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['link'] = $request->link_edit;
        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete Service page item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicePageItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => [__('Section not found!')]]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
        delete_file($image_link);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }


    /**
     * Method for show contact us section page
     * @param string $slug
     * @return view
     */
    public function giftCardView($slug)
    {
        $page_title = __("Gift Card Section");
        $section_slug = Str::slug(SiteSectionConst::GIFT_CARD_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;
        return view('admin.sections.setup-sections.gift-card-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update contact us section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function giftCardUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'description'       => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::GIFT_CARD_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request, $basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    /**
     * Method for show contact us section page
     * @param string $slug
     * @return view
     */
    public function airTimeView($slug)
    {
        $page_title = __("Mobile Topup Section");
        $section_slug = Str::slug(SiteSectionConst::AIR_TIME_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;
        return view('admin.sections.setup-sections.gift-card-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update contact us section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function airTimeUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'description'       => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::AIR_TIME_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request, $basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }



    /**
     * Method for show Blog section page
     * @param string $slug
     * @return view
     */
    public function blogView($slug)
    {
        $page_title = __("Blog Section");
        $section_slug = Str::slug(SiteSectionConst::BLOG_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        $blogs = Blogs::get();
        $categories = BlogsCategory::get();

        $total_categories = $categories->count();
        $active_categories = $categories->where("status", GlobalConst::ACTIVE)->count();

        $total_blogs = $blogs->count();
        $active_blogs = $blogs->where("status", GlobalConst::ACTIVE)->count();

        return view('admin.sections.setup-sections.blog-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
            'total_categories',
            'active_categories',
            'total_blogs',
            'active_blogs',
        ));
    }

    /**
     * Method for update Blog section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function blogUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'heading'       => "required|string",
            'sub_heading'   => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::BLOG_SECTION);
        $section = SiteSections::where("key", $slug)->first();
        $data['image'] = $section->value->image ?? null;
        if ($request->hasFile("image")) {
            if ($data['image']) {
                $image_link = get_files_path('site-section') . '/' . $section->value->image;
                delete_file($image_link);
            }
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }



    /**
     * Method for show about us section page
     * @param string $slug
     * @return view
     */
    public function aboutUsView($slug)
    {
        $page_title = __("About Us Section");
        $section_slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.about-us-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update about section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUsUpdate(Request $request, $slug)
    {

        $basic_field_name = [
            'heading'       => "required|string",
            'sub_heading'   => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }

        $data['image'] = $section->value->image ?? null;
        if ($request->hasFile("image")) {
            if ($data['image']) {
                $image_link = get_files_path('site-section') . '/' . $data['image'];
                delete_file($image_link);
            }
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    /**
     * Method for show footer section page
     * @param string $slug
     * @return view
     */
    public function footerView($slug)
    {
        $page_title = __("Footer Section");
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.footer-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update footer section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function footerUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'footer_desc'      => "required|string|max:1000",
        ];

        $validated = Validator::make($request->all(), [
            'icon'              => "required|array",
            'icon.*'            => "required|string|max:200",
            'link'              => "required|array",
            'link.*'            => "required|string|max:255",
        ])->validate();

        // generate input fields
        $social_links = [];
        foreach ($validated['icon'] as $key => $icon) {
            $social_links[] = [
                'icon'          => $icon,
                'link'          => $validated['link'][$key] ?? "",
            ];
        }

        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        $data['image'] = $section->value->image ?? null;
        if ($request->hasFile("image")) {
            if ($data['image']) {
                $image_link = get_files_path('site-section') . '/' . $data['image'];
                delete_file($image_link);
            }
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']   = $this->contentValidate($request, $basic_field_name);

        $data['contact']['social_links']    = $social_links;

        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);

        try {
            SiteSections::updateOrCreate(['key' => $slug], [
                'key'   => $slug,
                'value' => $data,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }



    /**
     * Method for show contact us section page
     * @param string $slug
     * @return view
     */
    public function contactUsView($slug)
    {
        $page_title = __("Contact US Section");
        $section_slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.contact-us-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update contact us section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function contactUsUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'heading'       => "required|string|max:100",
        ];

        $slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request, $basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for get languages form record with little modification for using only this class
     * @return array $languages
     */
    public function languages()
    {
        $languages = Language::whereNot('code', LanguageConst::NOT_REMOVABLE)->select("code", "name")->get()->toArray();
        $languages[] = [
            'name'      => LanguageConst::NOT_REMOVABLE_CODE,
            'code'      => LanguageConst::NOT_REMOVABLE,
        ];
        return $languages;
    }

    /**
     * Method for validate request data and re-decorate language wise data
     * @param object $request
     * @param array $basic_field_name
     * @return array $language_wise_data
     */
    public function contentValidate($request, $basic_field_name, $modal = null)
    {
        $languages = $this->languages();

        $current_local = get_default_language_code();
        $validation_rules = [];
        $language_wise_data = [];
        foreach ($request->all() as $input_name => $input_value) {
            foreach ($languages as $language) {
                $input_name_check = explode("_", $input_name);
                $input_lang_code = array_shift($input_name_check);
                $input_name_check = implode("_", $input_name_check);
                if ($input_lang_code == $language['code']) {
                    if (array_key_exists($input_name_check, $basic_field_name)) {
                        $langCode = $language['code'];
                        if ($current_local == $langCode) {
                            $validation_rules[$input_name] = $basic_field_name[$input_name_check];
                        } else {
                            $validation_rules[$input_name] = str_replace("required", "nullable", $basic_field_name[$input_name_check]);
                        }
                        $language_wise_data[$langCode][$input_name_check] = $input_value;
                    }
                    break;
                }
            }
        }
        if ($modal == null) {
            $validated = Validator::make($request->all(), $validation_rules)->validate();
        } else {
            $validator = Validator::make($request->all(), $validation_rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with("modal", $modal);
            }
            $validated = $validator->validate();
        }

        return $language_wise_data;
    }

    /**
     * Method for validate request image if have
     * @param object $request
     * @param string $input_name
     * @param string $old_image
     * @return boolean|string $upload
     */
    public function imageValidate($request, $input_name, $old_image)
    {
        if ($request->hasFile($input_name)) {
            $image_validated = Validator::make($request->only($input_name), [
                $input_name         => "image|mimes:png,jpg,webp,jpeg,svg",
            ])->validate();

            $image = get_files_from_fileholder($request, $input_name);
            $upload = upload_files_from_path_dynamic($image, 'site-section', $old_image);
            return $upload;
        }

        return false;
    }
}
