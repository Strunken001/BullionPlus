<?php

namespace App\Http\Controllers\User;

use App\Constants\GlobalConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\SetupKyc;
use App\Models\UserAuthorization;
use App\Providers\Admin\BasicSettingsProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ControlDynamicInputFields;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class AuthorizationController extends Controller
{
    use ControlDynamicInputFields;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMailFrom($token)
    {
        $page_title = setPageTitle("Mail Authorization");
        return view('user.auth.authorize.verify-mail', compact("page_title", "token"));
    }

    /**
     * Verify authorization code.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mailVerify(Request $request, $token)
    {
        $request->merge(['token' => $token]);
        $request->validate([
            'token'     => "required|string|exists:user_authorizations,token",
            'code'      => "required|numeric|exists:user_authorizations,code",
        ]);
        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = UserAuthorization::where("token", $request->token)->where("code", $request->code)->first();
        if ($auth_column->created_at->addSeconds($otp_exp_sec) < now()) {
            $this->authLogout($request);
            return redirect()->route('user.login')->with(['error' => [__('Session expired. Please try again')]]);
        }

        try {
            $auth_column->user->update([
                'email_verified'    => true,
            ]);
            $auth_column->delete();
        } catch (Exception $e) {
            $this->authLogout($request);
            return redirect()->route('user.login')->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->intended(route("user.dashboard"))->with(['success' => [__('Account successfully verified')]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMobileForm($token)
    {
        $page_title = setPageTitle("Mobile Authorization");
        return view('user.auth.authorize.verify-mobile', compact("page_title", "token"));
    }


    /**
     * Verify authorization code.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mobileVerify(Request $request, $token)
    {
        $otp = join($request->otp);

        $request->merge(['token' => $token, 'otp' => $otp,]);
        $request->validate([
            'token'     => "required|string|exists:user_authorizations,token",
            'otp'      => "required|numeric|exists:user_authorizations,code",
        ]);
        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = UserAuthorization::where("token", $request->token)->where("code", $request->otp)->first();
        if ($auth_column->created_at->addSeconds($otp_exp_sec) < now()) {
            $this->authLogout($request);
            return redirect()->route('user.login')->with(['error' => [__('Session expired. Please try again')]]);
        }

        try {
            $auth_column->user->update([
                'sms_verified'    => true,
            ]);
            $auth_column->delete();
        } catch (Exception $e) {
            $this->authLogout($request);
            return redirect()->route('user.login')->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->intended(route("user.dashboard"))->with(['success' => [__('Account successfully verified')]]);
    }

    /**
     * Verify authorization code.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resendMobileOTP($token)
    {
        $auth_column = UserAuthorization::where("token", $token)->first();
        if (!$auth_column) return back()->with(['error' => [__('Request token is invalid')]]);
        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        if ($auth_column->created_at->addSeconds($otp_exp_sec) < now()) {
            return redirect()->route('user.authorize.mobile', $token)->with(['error' => [__('Session expired. Please try again')]]);
        }
        if (Carbon::now() <= Carbon::parse($auth_column->created_at)->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            throw ValidationException::withMessages([
                'code'      => 'You can resend verification code after ' . Carbon::now()->diffInSeconds(Carbon::parse($auth_column->created_at)->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) . ' seconds',
            ]);
        }
        $code               = generate_random_code();
        $data = [
            'user_id'       => $auth_column->user_id,
            'code'          => $code,
            'token'         => $token,
            'created_at'    => now(),
        ];


        DB::beginTransaction();
        try {
            DB::table("user_authorizations")->where('token', $token)->update($data);
            DB::commit();
            $message = __("Your verification code is :code", ['code' => $code]);
            sendAuthSms(auth()->user(), $message);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with(['error' => [__("Something went wrong! Please try again.")]]);
        }
        return redirect()->route('user.authorize.mobile', $data['token'])->with(['warning' => [__("OTP resend success")]]);
    }

    public function authLogout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        auth()->guard("web")->logout();
    }

    public function showKycFrom()
    {
        $user = auth()->user();
        if ($user->kyc_verified == GlobalConst::VERIFIED) return back()->with(['success' => [__('You are already KYC Verified User')]]);
        $page_title = setPageTitle("KYC Verification");
        $user_kyc = SetupKyc::userKyc()->first();
        if (!$user_kyc) return back();
        $kyc_data = $user_kyc->fields;
        $kyc_fields = [];
        if ($kyc_data) {
            $kyc_fields = array_reverse($kyc_data);
        }
        return view('user.auth.authorize.verify-kyc', compact("page_title", "kyc_fields"));
    }

    public function kycSubmit(Request $request)
    {

        $user = auth()->user();
        if ($user->kyc_verified == GlobalConst::VERIFIED) return back()->with(['success' => [__('You are already KYC Verified User')]]);

        $user_kyc_fields = SetupKyc::userKyc()->first()->fields ?? [];
        $validation_rules = $this->generateValidationRules($user_kyc_fields);

        $validated = Validator::make($request->all(), $validation_rules)->validate();
        $get_values = $this->placeValueWithFields($user_kyc_fields, $validated);

        $create = [
            'user_id'       => auth()->user()->id,
            'data'          => json_encode($get_values),
            'created_at'    => now(),
        ];

        DB::beginTransaction();
        try {
            DB::table('user_kyc_data')->updateOrInsert(["user_id" => $user->id], $create);
            $user->update([
                'kyc_verified'  => GlobalConst::PENDING,
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $user->update([
                'kyc_verified'  => GlobalConst::DEFAULT,
            ]);
            $this->generatedFieldsFilesDelete($get_values);
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return redirect()->route("user.profile.index")->with(['success' => [__('KYC information successfully submitted')]]);
    }

    public function showGoogle2FAForm()
    {
        $page_title =  "Authorize Google Two Factor";
        return view('user.auth.authorize.verify-google-2fa', compact('page_title'));
    }

    public function google2FASubmit(Request $request)
    {

        $otp = join($request->otp);
        $request->merge(['otp' => $otp,]);
        $request->validate([
            'otp'      => "required",
        ]);

        $code = $request->otp;

        $user = auth()->user();

        if (!$user->two_factor_secret) {
            return back()->with(['warning' => [__('Your secret key not stored properly. Please contact with system administrator')]]);
        }

        // dd($user->two_factor_secret,$code);
        if (google_2fa_verify($user->two_factor_secret, $code)) {

            $user->update([
                'two_factor_verified'   => true,
            ]);

            return redirect()->intended(route('user.dashboard'));
        }

        return back()->with(['warning' => [__('Failed to login. Please try again')]]);
    }
}
