<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\GlobalConst;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Traits\User\LoggedInUsers;
use Exception;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    protected $request_data;
    protected $lockoutTime = 1;

    use AuthenticatesUsers, LoggedInUsers;

    public function showLoginForm()
    {
        $page_title = setPageTitle("User Login");
        return view('user.auth.login', compact(
            'page_title',
        ));
    }



    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // dd($request->all());
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // Checks is if loging is by otp or password
        if ($request->switch_one == "OTP") {
            $phone = $request->otp_country . $request->otp_number;
            $data = $request->except('otp_number');
            $data['otp_number'] = $phone;
            $user = getUser($data);
            $stay = $request->stay ?? 0;
            return loginVerificationTemplate($user['user'], $stay);
        } else {
            if ($this->attemptLogin($request)) {
                if ($request->hasSession()) {
                    $request->session()->put('auth.password_confirmed_at', time());
                }
                return $this->sendLoginResponse($request);
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }


    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        // $this->request_data = $request;

        // Checks is if loging is by otp or password
        if ($request->switch_one == "OTP") {
            $rules = [
                'otp_number' => 'required|string|exists:users,mobile',
                'otp_country' => 'required|string|exists:users,mobile_code',
            ];
            $messages = [
                'otp_number.required' => 'Your phone number is required',
                'otp_country.required' => 'Your country code is required',
                'otp_number.exists' => 'Credentials do not match',
                'otp_country.exists' => 'Country code do not match',
            ];
        } else {
            $rules = [
                'password_number' => 'required|string|exists:users,mobile',
                'pass_country' => 'required|string|exists:users,mobile_code',
                'password' => 'required|string',
            ];
            $messages = [
                'pass_country.required' => 'Your country code is required',
                'pass_country.exists' => 'Country code do not match',
                'password_number.required' => 'Your phone number is required',
                'password.required' => 'Your password is required',
            ];
        }

        $validator = Validator::make($request->only(array_keys($rules)), $rules, $messages);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    }


    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        if ($request->switch_one == "OTP") {
            $request->merge(['status' => true]);
            $request->merge([$this->username() => $request->otp_number]);
            return $request->only($this->username(), 'status');
        } else {
            $full_mobile = $request->pass_country . $request->password_number;
            $request->merge(['status' => true]);
            $request->merge([$this->username() => $full_mobile]);
            return $request->only($this->username(), 'password', 'status');
        }
    }


    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'full_mobile';
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            "credentials" => [trans('auth.failed')],
        ]);
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard("web");
    }


    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request = null, $user)
    {
        $user->update([
            'two_factor_verified'   => false,
        ]);

        $this->refreshUserWallets($user);
        $this->createLoginLog($user);
        return redirect()->intended(route('user.dashboard'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showOTPForm($token, $user, $stay, $time)
    {
        $page_title = setPageTitle("Login Authorization");
        $re_time = User::where('id', $user)->first();
        $resend_time = 0;
        if (Carbon::now() <= $re_time->ver_code_send_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            $resend_time = Carbon::now()->diffInSeconds($re_time->ver_code_send_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE));
        }
        return view('user.auth.authorize.verify-login', compact("page_title", "token", 'user', 'stay', 'resend_time'));
    }


    /**
     * Verify authorization code.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginVerify(Request $request, $token, $user, $stay)
    {
        $otp = join($request->otp);
        $request->merge(['token' => $token, 'otp' => $otp,]);
        $request->validate([
            'otp'      => "required|numeric|exists:users,ver_code",
        ]);
        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = User::where("id", $user)->where("ver_code", $request->otp)->first();
        if (Carbon::parse($auth_column->ver_code_send_at)->addSeconds($otp_exp_sec) < now()) {
            $this->authLogout($request);
            return redirect()->route('user.login')->with(['error' => [__('Session expired. Please try again')]]);
        }
        $auth_column->update([
            'sms_verified'  => true,
        ]);
        return $this->otpLogin($auth_column, $stay);
    }

    public function resendLoginCode($token, $user, $stay)
    {
        $resend_code = User::where("id", $user)->first();
        if (!$resend_code) return back()->with(['error' => [__('Request token is invalid')]]);
        if (Carbon::now() <= Carbon::parse($resend_code->ver_code_send_at)->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            throw ValidationException::withMessages([
                'code'      => 'You can resend verification code after ' . Carbon::now()->diffInSeconds(Carbon::parse($resend_code->ver_code_send_at)->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) . ' seconds',
            ]);
        }
        DB::beginTransaction();
        try {
            $update_data = [
                'code'          => generate_random_code(),
                'created_at'    => now(),
                'token'         => $token,
            ];
            $user_info = DB::table('users')->where('id', $user);
            $user_info->update([
                'ver_code'          =>  $update_data['code'],
                'ver_code_send_at'  =>  $update_data['created_at'],
            ]);
            $message = __("Your verification code is: " . $update_data['code']);
            $re_time = User::where('id', $user)->first();
            $resend_time = 0;
            if (Carbon::now() <= $re_time->ver_code_send_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
                $resend_time = Carbon::now()->diffInSeconds($re_time->ver_code_send_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE));
            }
            sendAuthSms($user_info->first(),$message);
            DB::commit();
        } catch (Exception $e) {
            // dd($e);
            DB::rollback();
            return back()->with(['error' => [__('Something went wrong. please try again')]]);
        }
        return redirect()->route('user.login.OTP', ['token' => $token, 'user' => $user, 'stay' => $stay, 'time'=> $resend_time])->with(['success' => [__('Verification code resend success!')]]);
    }


    public function otpLogin($user, $stay)
    {
        if (Auth::guard('web')->loginUsingId($user->id, $stay)) {
            $user->update([
                'ver_code'              => null,
                'ver_code_send_at'      => null,
            ]);
            return $this->authenticated($request = null, $user);
        }
    }
}
