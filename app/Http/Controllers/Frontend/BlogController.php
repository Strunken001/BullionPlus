<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Admin\SiteSections;
use App\Providers\Admin\BasicSettingsProvider;
use App\Constants\SiteSectionConst;
use App\Models\Admin\Language;
use App\Models\Frontend\Blogs;
use Exception;
use Illuminate\Http\RedirectResponse;
use App\Constants\GlobalConst;
use App\Constants\LanguageConst;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\Validator;

use App\Models\Frontend\BlogsCategory;

class BlogController extends Controller
{
    public function blog(BasicSettingsProvider $basic_settings)
    {
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $section_slug = Str::slug(SiteSectionConst::BLOG_SECTION);
        $blog       = SiteSections::getData($section_slug)->first();
        $blogs      = Blogs::where('status',1)->get();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        return view('frontend.pages.blog',compact('page_title','blog','blogs','footer'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function categoryIndex()
    {
        $page_title = __("Blog Category");
        $categories = BlogsCategory::orderByDesc("id")->get();
        $languages = Language::get();
        return view('admin.sections.setup-sections.blog-post.category.index',compact('page_title','categories','languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function categoryStore(Request $request)
    {
        $basic_field_name = [
            'name'          => "required|string|max:150",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"category-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $data['language'] = $language_wise_data;

        try{
            BlogsCategory::create([
                'admin_id'      => auth()->user()->id,
                'name'          => $data,
                'created_at'    => now(),
                'status'        => true,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Category added successfully!')]]);
    }

    public function categoryUpdate(Request $request) {
        $validated = $request->validate([
            'target'    => "required|numeric|exists:blogs_categories,id",
        ]);

        $basic_field_name = [
            'name_edit'          => "required|string|max:250",
        ];

        $category = BlogsCategory::find($validated['target']);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"category-update");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $data['language']  = $language_wise_data;

        try{
            $category->update([
                'name'      => $data,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Category updated successfully!')]]);
    }


    public function categoryStatusUpdate(Request $request) {
        $validator = Validator::make($request->all(), [
            'status'                    => 'required|boolean',
            'input_name'                => 'required|string',
            'data_target'               => 'required|integer|exists:blogs_categories,id',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            $error = ['error' => $validator->errors()];
            return Response::error($error, null, 400);
        }
        $validated = $validator->validate();


        try {
            $category = BlogsCategory::find($validated['data_target']);
            if($category) {
                $category->update([
                    'status'    => ($validated['status'] == true) ? false : true,
                ]);
            }
        } catch (Exception $e) {
            $error = ['error' => [__('Something went wrong! Please try again.')]];
            return Response::error($error, null, 500);
        }

        $success = ['success' => [__('Category status updated successfully!')]];
        return Response::success($success, null, 200);
    }

    /**
     * Remove the specified resource from record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function categoryDelete(Request $request)
    {
        $request->validate([
            'target'    => "required|integer|exists:blogs_categories,id",
        ]);

        try{
            $category = BlogsCategory::find($request->target);
            if($category) $category->delete();
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Category deleted successfully!')]]);
    }

    public function blogIndex() {
        $page_title = __("Blogs");
        $blogs = Blogs::orderByDesc("id")->get();

        return view('admin.sections.setup-sections.blog-post.index',compact('page_title','blogs'));
    }

    public function blogCreate() {
        $page_title = __("Create New Blog");
        $categories = BlogsCategory::orderByDesc("id")->where("status",GlobalConst::ACTIVE)->get();
        $languages = Language::get();

        return view('admin.sections.setup-sections.blog-post.create',compact("page_title","categories","languages"));
    }

    public function blogStore(Request $request) {
        $basic_field_name = [
            'title'         => "required|string|max:255",
            'description'   => "required|string|max:5000000",
            'tags'          => "required|array",
        ];

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $validated = Validator::make($request->all(),[
            'category'  => "required|integer|exists:blogs_categories,id",
        ])->validate();

        // make slug
        $not_removable_lang = LanguageConst::NOT_REMOVABLE;
        $slug_text = $data['language'][$not_removable_lang]['title'] ?? "";
        if($slug_text == "") {
            $slug_text = $data['language'][get_default_language_code()]['title'] ?? "";
            if($slug_text == "") {
                $slug_text = Str::uuid();
            }
        }
        $slug = Str::slug(Str::lower($slug_text));

        if(Blogs::where('slug',$slug)->exists()) return back()->with(['error' => [__('Blog title is similar. Please update/change this title')]]);

        $data['image'] = null;
        if($request->hasFile("image")) {
            $data['image']  = $this->imageValidate($request,"image",null);
        }

        try{
            Blogs::create([
                'admin_id'                  =>  auth()->user()->id,
                'slug'                      => $slug,
                'category_id'               => $validated['category'],
                'data'                      => $data,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->route('admin.setup.sections.blog.index')->with(['success' => [__('Blog created successfully!')]]);
    }

    public function blogStatusUpdate(Request $request) {
        $validator = Validator::make($request->all(), [
            'status'                    => 'required|boolean',
            'input_name'                => 'required|string',
            'data_target'               => 'required|integer|exists:blogs,id',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            $error = ['error' => $validator->errors()];
            return Response::error($error, null, 400);
        }
        $validated = $validator->validate();

        try {
            $blog = Blogs::find($validated['data_target']);
            if($blog) {
                $blog->update([
                    'status'    => ($validated['status'] == true) ? false : true,
                ]);
            }
        } catch (Exception $e) {
            $error = ['error' => [__('Something went wrong! Please try again.')]];
            return Response::error($error, null, 500);
        }

        $success = ['success' => [__('Blog status updated successfully!')]];
        return Response::success($success, null, 200);
    }

    public function blogDelete(Request $request) {
        $request->validate([
            'target'    => "required|integer|exists:blogs,id"
        ]);

        try{
            $blog = Blogs::find($request->target);
            if($blog) {
                $image_name = $blog->data?->image ?? null;
                if($image_name) {
                    $image_link = get_files_path('site-section') . "/" . $image_name;
                    delete_file($image_link);
                }
                $blog->delete();
            }
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return back()->with(['success' => [__('Blog deleted successfully!')]]);
    }

    public function blogEdit($id) {
        $blog = Blogs::find($id);
        if(!$blog) return back()->with(['error' => [__("Blog does't exists!")]]);
        $page_title = __("Blog Edit");
        $languages = Language::get();
        $categories = BlogsCategory::where("status",GlobalConst::ACTIVE)->orderByDesc("id")->get();
        return view('admin.sections.setup-sections.blog-post.edit',compact("page_title","blog","languages","categories"));
    }

    public function blogUpdate(Request $request,$id) {

        $blog = Blogs::find($id);
        if(!$blog) return back()->with(['error' => [__("Blog does't exists!")]]);

        $basic_field_name = [
            'title'         => "required|string|max:255",
            'description'   => "required|string|max:5000000",
            'tags'          => "required|array",
        ];

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $validated = Validator::make($request->all(),[
            'category'  => "required|integer|exists:blogs_categories,id",
        ])->validate();

        $data['image'] = $blog->data?->image ?? null;
        if($request->hasFile("image")) {
            $data['image']  = $this->imageValidate($request,"image",$data['image']);
        }

        try{
            $blog->update([
                'category_id'  => $validated['category'],
                'data'                      => $data,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->route('admin.setup.sections.blog.index')->with(['success' => [__('Blog updated successfully!')]]);
    }

    /**
     * Method for validate request data and re-decorate language wise data
     * @param object $request
     * @param array $basic_field_name
     * @return array $language_wise_data
     */
    public function contentValidate($request,$basic_field_name,$modal = null) {
        $languages = Language::get();

        $current_local = get_default_language_code();
        $validation_rules = [];
        $language_wise_data = [];
        foreach($request->all() as $input_name => $input_value) {
            foreach($languages as $language) {
                $input_name_check = explode("_",$input_name);
                $input_lang_code = array_shift($input_name_check);
                $input_name_check = implode("_",$input_name_check);
                if($input_lang_code == $language['code']) {
                    if(array_key_exists($input_name_check,$basic_field_name)) {
                        $langCode = $language['code'];
                        if($current_local == $langCode) {
                            $validation_rules[$input_name] = $basic_field_name[$input_name_check];
                        }else {
                            $validation_rules[$input_name] = str_replace("required","nullable",$basic_field_name[$input_name_check]);
                        }
                        $language_wise_data[$langCode][$input_name_check] = $input_value;
                    }
                    break;
                }
            }
        }
        if($modal == null) {
            $validated = Validator::make($request->all(),$validation_rules)->validate();
        }else {
            $validator = Validator::make($request->all(),$validation_rules);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with("modal",$modal);
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
    public function imageValidate($request,$input_name,$old_image = null) {
        if($request->hasFile($input_name)) {
            $image_validated = Validator::make($request->only($input_name),[
                $input_name         => "image|mimes:png,jpg,webp,jpeg,svg",
            ])->validate();

            $image = get_files_from_fileholder($request,$input_name);
            $upload = upload_files_from_path_dynamic($image,'site-section',$old_image);
            return $upload;
        }
        return false;
    }

    /**
     * Displays the details of a single blog post.
     *
     * @param BasicSettingsProvider $basic_settings The basic settings provider.
     * @param int $id The ID of the blog post to display.
     * @return \Illuminate\Contracts\View\View The view for the blog single page.
     */
    public function blogSingle(BasicSettingsProvider $basic_settings,$id)
    {
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $blog      = Blogs::active()->where('id',$id)->first();
        $recent_blogs  = Blogs::active()->latest()->limit(3)->get();
        $categories   = BlogsCategory::where('status',1)->get();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        return view('frontend.pages.blog-single',compact('page_title','blog','categories','recent_blogs','footer'));
    }

    public function blogCategory(BasicSettingsProvider $basic_settings,$category)
    {
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $category_blogs      = Blogs::active()->where('category_id',$category)->get();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        return view('frontend.pages.category-blog',compact('page_title','category_blogs','footer'));
    }
}
