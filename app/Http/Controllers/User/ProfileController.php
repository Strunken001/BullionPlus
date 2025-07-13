<?php

namespace App\Http\Controllers\User;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\Admin\SetupKyc;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Constants\SiteSectionConst;
use App\Models\Admin\SiteSections;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "User Profile";
        $kyc_data = SetupKyc::userKyc()->first();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        return view('user.page.profile', compact("page_title", "kyc_data", "footer"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user_id = auth()->user()->id;

        $validated = Validator::make($request->all(), [
            'firstname'     => "required|string|max:60",
            'lastname'      => "required|string|max:60",
            'country'       => "required|string|max:50",
            'phone_code'    => "required|string|max:20",
            'mobile'        => 'required|string|max:20|unique:users,mobile,' . $user_id,
            'state'         => "nullable|string|max:50",
            'city'          => "nullable|string|max:50",
            'zip_code'      => "nullable|numeric",
            'address'       => "nullable|string|max:250",
            'image'         => "nullable|image|mimes:jpg,png,svg,webp|max:10240",
            'email'         => 'required|string|email|max:150',
        ])->validate();

        $validated['mobile']        = remove_speacial_char($validated['mobile']);
        $validated['mobile_code']   = remove_speacial_char($validated['phone_code']);
        $complete_phone             = $validated['mobile_code'] . $validated['mobile'];
        $validated['full_mobile']   = $complete_phone;
        $validated                  = Arr::except($validated, ['agree', 'phone_code', 'phone']);
        $validated['address']       = [
            'country'   => $validated['country'],
            'state'     => $validated['state'] ?? "",
            'city'      => $validated['city'] ?? "",
            'zip'       => $validated['zip_code'] ?? "",
            'address'   => $validated['address'] ?? "",
        ];

        if ($request->hasFile("image")) {
            $image = upload_file($validated['image'], 'user-profile', auth()->user()->image);
            $upload_image = upload_files_from_path_dynamic([$image['dev_path']], 'user-profile');
            delete_file($image['dev_path']);
            $validated['image']     = $upload_image;
        }

        try {
            auth()->user()->update($validated);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Profile successfully updated!')]]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function passwordChange()
    {
        $page_title = "User Password Change";
        $kyc_data = SetupKyc::userKyc()->first();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        return view('user.page.password-change', compact("page_title", "kyc_data", "footer"));
    }

    public function passwordUpdate(Request $request)
    {

        $basic_settings = BasicSettingsProvider::get();
        $password_rule = "required|string|min:6|confirmed";
        if ($basic_settings->secure_password) {
            $password_rule = ["required", Password::min(8)->letters()->mixedCase()->numbers()->symbols(), "confirmed"];
        }

        $request->validate([
            'current_password'      => "required|string",
            'password'              => $password_rule,
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            throw ValidationException::withMessages([
                'current_password'      => __('Current password didn\'t match'),
            ]);
        }

        try {
            auth()->user()->update([
                'password'  => Hash::make($request->password),
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Password successfully updated!')]]);
    }


    public function profileDelete()
    {

        $user = User::where('id', auth()->user()->id)->first();
        if (!$user) return back()->with(['error' => [__('profile could not find')]]);

        try {
            auth()->user()->update([
                'status'  => 0,
            ]);
            Auth::logout();
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Profile successfully deleted!')]]);
    }
}
