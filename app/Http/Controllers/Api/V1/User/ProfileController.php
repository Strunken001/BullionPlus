<?php

namespace App\Http\Controllers\Api\V1\User;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    public function profileInfo()
    {
        $user = auth()->guard("api")->user();

        unset($user->two_factor_secret);
        unset($user->ver_code);
        unset($user->ver_code_send_at);

        $response_data = $user;

        $response_data['country']        = $user->address->country ?? "";
        $response_data['kyc']            = [
            'data'          => $user->kyc->data ?? [],
            'reject_reason' => $user->kyc->reject_reason ?? "",
        ];

        $image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("user-profile"),
            'default_image'     => files_asset_path_basename("profile-default"),
        ];

        $instructions = [
            'kyc_verified'      => "0: Default, 1: Approved, 2: Pending, 3:Rejected",
        ];

        return Response::success([__('Profile info fetch successfully!')], [
            'instructions'  => $instructions,
            'user_info'     => $response_data,
            'image_paths'   => $image_paths,
            'countries'     => get_all_countries(['id', 'name', 'mobile_code']),
        ], 200);
    }

    public function profileInfoUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname'     => "required|string|max:60",
            'lastname'      => "required|string|max:60",
            'country'       => "required|string|max:50",
            'mobile_code'   => "required|string|max:20",
            'email'         => "required|email",
            'mobile'        => "required|string|max:20",
            'address'       => "nullable|string|max:250",
            'image'         => "nullable|max:10240",
            'state'       => "nullable|string",
            'city'       => "nullable|string",
            'zip'       => "nullable|string",
        ]);

        if ($validator->fails()) return Response::error($validator->errors()->all(), []);

        $validated = $validator->validate();
        $validated['mobile']        = get_only_numeric_data($validated['mobile']);
        $validated['mobile_code']   = get_only_numeric_data($validated['mobile_code']);
        $complete_phone             = $validated['mobile_code'] . $validated['mobile'];
        $validated['full_mobile']   = $complete_phone;

        $user = auth()->guard(get_auth_guard())->user();

        if (User::whereNot('id', $user->id)->where("full_mobile", $validated['full_mobile'])->exists()) {
            return Response::error([__('Phone number already exists')], [], 400);
        }

        // $validated['address']       = $user->address;
        $validated['address'] = [
            'country' => $request['country'] ? $validated['country'] : ($user->address->country ?? ''),
            'state'   => $request['state'] ? $validated['state'] : ($user->address->state ?? ''),
            'city'    => $request['city'] ? $validated['city'] : ($user->address->city ?? ''),
            'zip'     => $request['zip_code'] ? $validated['zip_code'] : ($user->address->zip ?? ''),
            'address' => $request['address'] ? $validated['address'] : ($user->address->address ?? ''),
        ];


        if (User::whereNot('id', $user->id)->where("email", $validated['email'])->exists()) {
            return Response::error([__('Email already exists')], [], 400);
        }

        if (User::whereNot('id', $user->id)->where("full_mobile", $validated['full_mobile'])->exists()) {
            return Response::error([__('Phone number already exists')], [], 400);
        }

        if ($request->hasFile("image")) {
            $image = upload_file($validated['image'], 'junk-files', $user->image);
            $upload_image = upload_files_from_path_dynamic([$image['dev_path']], 'user-profile');
            delete_file($image['dev_path']);
            $validated['image']     = $upload_image;
        }
        try {
            $user->update($validated);
        } catch (Exception $e) {
            return Response::error([__("Something went wrong! Please try again")], [], 500);
        }

        return Response::success([__('Profile successfully updated!')], [], 200);
    }

    public function profilePasswordUpdate(Request $request)
    {
        $basic_settings = BasicSettingsProvider::get();
        $password_rule = "required|string|min:6|confirmed";
        if ($basic_settings->secure_password) {
            $password_rule = ["required", Password::min(8)->letters()->mixedCase()->numbers()->symbols(), "confirmed"];
        }

        $validator = Validator::make($request->all(), [
            'current_password'      => "required|string",
            'password'              => $password_rule,
        ]);

        if ($validator->fails()) return Response::error($validator->errors()->all(), []);
        $validated = $validator->validate();

        if (!Hash::check($validated['current_password'], auth()->guard("api")->user()->password)) {
            return Response::error([__("Current password didn't match")], [], 400);
        }

        try {
            auth()->guard("api")->user()->update([
                'password'  => Hash::make($validated['password']),
            ]);
        } catch (Exception $e) {
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }

        return Response::success([__('Password successfully updated!')], [], 200);
    }

    public function deleteProfile(Request $request)
    {
        $user = Auth::guard(get_auth_guard())->user();
        if (!$user) {
            $message = ['success' =>  [__("Oops! User does not exists")]];
            return Response::error($message, []);
        }
        try {
            $user->status            = 0;
            $user->deleted_at        = now();
            $user->save();
        } catch (Exception $e) {
            return Response::error([__('Something went wrong, please try again!')], []);
        }
        return Response::success([__('Your account deleted successfully!')], $user);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        return Response::success([__('Logout success!')], [], 200);
    }

    public function refreshToken(Request $request)
    {
        $token = JWTAuth::parseToken()->refresh();

        return Response::success([_("Token refreshed")], ["token" => $token], 200);
    }
}
