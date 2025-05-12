<?php

namespace App\Http\Controllers\User\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\UserPasswordReset;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Admin\SiteSections;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Validation\ValidationException;
use App\Notifications\User\Auth\PasswordResetEmail;

class ForgotPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showForgotForm()
    {
        $page_title = setPageTitle("Forgot Password");
        $forget_des = SiteSections::where('key','auth-section')->first();
        return view('user.auth.forgot-password.forgot',compact('page_title','forget_des'));
    }

    /**
     * Send Verification code to user email/phone.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'number'   => "required|string|exists:users,mobile",
            'otp_country' => 'required|string|exists:users,mobile_code',
        ]);
        $phone = $request->otp_country . $request->number;
        $column = "full_mobile";
        if(check_email($request->credentials)) $column = "email";
        $user = User::where($column,$phone)->first();
        if(!$user) {
            throw ValidationException::withMessages([
                'credentials'       => "User doesn't exists.",
            ]);
        }
        $token = generate_unique_string("user_password_resets","token",80);
        $code = generate_random_code();

        try{
            UserPasswordReset::where("user_id",$user->id)->delete();
            $password_reset = UserPasswordReset::create([
                'user_id'       => $user->id,
                'token'         => $token,
                'code'          => $code,
            ]);
            // sendSms($user,'PASS_RESET_CODE', $code);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return redirect()->route('user.password.forgot.code.verify.form',['token' => $token, 'mobile' => $user->full_mobile])->with(['success' => [__('Verification code sended to your mobile number.')]]);
    }


    public function showVerifyForm($token,$mobile) {
        $page_title = setPageTitle("Verify User");
        $password_reset = UserPasswordReset::where("token",$token)->first();
        if(!$password_reset) return redirect()->route('user.password.forgot')->with(['error' => [__('Password Reset Token Expired')]]);
        $resend_time = 0;
        if(Carbon::now() <= $password_reset->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            $resend_time = Carbon::now()->diffInSeconds($password_reset->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE));
        }
        return view('user.auth.forgot-password.verify',compact('page_title','token','resend_time','mobile'));
    }

    /**
     * OTP Verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyCode(Request $request,$token)
    {
        $otp = join($request->otp);
        $request->merge(['token' => $token, 'otp' => $otp]);
        $validated = Validator::make($request->all(),[
            'token'         => "required|string|exists:user_password_resets,token",
            'otp'          => "required|numeric|exists:user_password_resets,code",
        ])->validate();

        $basic_settings = BasicSettingsProvider::get();
        $otp_exp_seconds = $basic_settings->otp_exp_seconds ?? 0;

        $password_reset = UserPasswordReset::where("token",$token)->first();

        if(Carbon::now() >= $password_reset->created_at->addSeconds($otp_exp_seconds)) {
            foreach(UserPasswordReset::get() as $item) {
                if(Carbon::now() >= $item->created_at->addSeconds($otp_exp_seconds)) {
                    $item->delete();
                }
            }
            return redirect()->route('user.password.forgot')->with(['error' => [__('Session expired. Please try again.')]]);
        }

        if($password_reset->code != $validated['otp']) {
            throw ValidationException::withMessages([
                'otp'      => "Verification Otp is Invalid",
            ]);
        }

        return redirect()->route('user.password.forgot.reset.form',$token);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resendCode($token,$mobile)
    {
        $password_reset = UserPasswordReset::where('token',$token)->first();
        if(!$password_reset) return back()->with(['error' => ['Request token is invalid']]);
        if(Carbon::now() <= $password_reset->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            throw ValidationException::withMessages([
                'code'      => 'You can resend verification code after '.Carbon::now()->diffInSeconds($password_reset->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)). ' seconds',
            ]);
        }

        DB::beginTransaction();
        try{
            $update_data = [
                'code'          => generate_random_code(),
                'created_at'    => now(),
                'token'         => $token,
            ];
            DB::table('user_password_resets')->where('token',$token)->update($update_data);
            $user = User::where('full_mobile',$mobile)->first();
            sendSms($user,'PASS_RESET_CODE', $update_data['code']);
            DB::commit();
        }catch(Exception $e) {
            DB::rollback();
            return back()->with(['error' => [__('Something went wrong. please try again')]]);
        }
        return redirect()->route('user.password.forgot.code.verify.form',['token' => $token,'mobile' => $mobile])->with(['success' => [__('Verification code resend success!')]]);

    }


    public function showResetForm($token) {
        $page_title = setPageTitle("Reset Password");
        return view('user.auth.forgot-password.reset',compact('page_title','token'));
    }


    public function resetPassword(Request $request,$token) {
        $basic_settings = BasicSettingsProvider::get();
        $password_rule = "required|string|min:6|confirmed";
        if($basic_settings->secure_password) {
            $password_rule = ["required",Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised(),"confirmed"];
        }

        $request->merge(['token' => $token]);
        $validated = Validator::make($request->all(),[
            'token'         => "required|string|exists:user_password_resets,token",
            'password'      => $password_rule,
        ])->validate();

        $password_reset = UserPasswordReset::where("token",$token)->first();
        if(!$password_reset) {
            throw ValidationException::withMessages([
                'password'      => __("Invalid Request. Please try again."),
            ]);
        }

        try{
            $password_reset->user->update([
                'password'      => Hash::make($validated['password']),
            ]);
            $password_reset->delete();
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return redirect()->route('user.login')->with(['success' => [__('Password reset success. Please login with new password.')]]);
    }

}
