<?php

namespace App\Http\Controllers\Api\V1\User\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use App\Traits\User\LoggedInUsers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Api\V1\User\Auth\AuthorizationController;
use App\Providers\Admin\BasicSettingsProvider;
use Carbon\Carbon;
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

    use AuthenticatesUsers, LoggedInUsers;

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
        $this->request_data = $request;

        $validator = Validator::make($request->only('switch'), ['switch' => 'required'], ['switch.required' => 'login type is required']);
        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), []);
        }

        if ($request->switch == "otp") {
            $rules = [
                'otp_number' => 'required|string|exists:users,full_mobile',
            ];
            $messages = [
                'otp_number.required' => 'Your phone number is required',
                'otp_number.exists' => 'Credentials do not match',
            ];
            $type = 'otp_number';
        } else {
            $rules = [
                'password_number' => 'required|string',
                'password' => 'required|string',
            ];
            $messages = [
                'password_number.required' => 'Your phone number is required',
                'password.required' => 'Your password is required',
            ];
            $type = 'password_number';
        }

        $validator = Validator::make($request->only(array_keys($rules)), $rules, $messages);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), []);
        }

        $validated = $validator->validate();
        if (!User::where($this->username(), $validated[$type])->exists()) {
            return Response::error([__("User doesn't exists!")], [], 404);
        }

        $user = User::where($this->username(), $validated[$type])->first();
        if (!$user) return Response::error([__("User doesn't exists!")]);


        if ($type === 'otp_number') {
            // User authenticated
            $this->loginVerification($user);
            return Response::success([__('Login verification code sent successfully')], [
                'type'          => $type,
                'user_info'     => $user->only(['id']),
                'authorization' => [],
            ], 200);
        } else {

            if (Hash::check($validated['password'], $user->password)) {
                if ($user->status != GlobalConst::ACTIVE) return Response::error([__("Your account is temporary banded. Please contact with system admin")]);
                // User authenticated
                $token = $user->createToken("auth_token")->accessToken;
                return $this->authenticated($user, $token, $type);
            }
        }
        return Response::error([__("Credentials didn't match")]);
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
            $request->merge(['status' => true]);
            $request->merge([$this->username() => $request->password_number]);
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
        return "full_mobile";
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard("api");
    }


    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated($user, $token, $type)
    {
        $user->update([
            'two_factor_verified'   => false,
        ]);

        try {
            $sms_response = [];
        } catch (Exception $e) {
            return Response::error([$e->getMessage()], [], 500);
        }

        try {
            $this->refreshUserWallets($user);
        } catch (Exception $e) {
            return Response::error([__('Login Failed! Failed to refresh wallet! Please try again')], [], 500);
        }
        $this->createLoginLog($user);

        return Response::success([__('User successfully logged in')], [
            'token'         => $token,
            'type'          => $type,
            'user_info'     => $user->only([
                'id',
                'firstname',
                'lastname',
                'fullname',
                'username',
                'email',
                'mobile_code',
                'mobile',
                'full_mobile',
                'sms_verified',
                'kyc_verified',
                'two_factor_verified',
                'two_factor_status',
                'two_factor_secret',
            ]),
            'authorization' => [
                'status'    => count($sms_response) > 0 ? true : false,
                'token'     => $sms_response['token'] ?? "",
            ],
        ], 200);
    }


    // login otp verification
    function loginVerification($user)
    {
        $data = [
            'user_id'       => $user->id,
            'code'          => generate_random_code(),
            'token'         => generate_unique_string("user_authorizations", "token", 200),
            'created_at'    => now(),
        ];
        DB::beginTransaction();
        try {
            DB::table('users')->where('id',  $data['user_id'])->update([
                'ver_code'          =>  $data['code'],
                'ver_code_send_at'  =>  $data['created_at'],
            ]);
            DB::commit();
            try {
                $message = __("Your verification code is: " . $data['code']);
                sendAuthSms($user, $message);
            } catch (Exception $e) {
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }
        return $data;
    }


    /**
     * Verify authorization code.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginVerify(Request $request)
    {
        $rules = [
            'user'     => "required|exists:users,id",
            'type'     => "required",
            'otp'      => "required|numeric|exists:users,ver_code",
        ];

        $messages = [
            'otp.required'      => 'OTP is required',
            'otp.numeric'       => 'OTP should be numeric',
            'otp.exists'        => 'OTP is invalid',
            'type.required'     => 'Login type is required',
            'user.required'     => 'User id is required',
            'user.exists'       => 'User dose not exist'
        ];
        $validator = Validator::make($request->only(array_keys($rules)), $rules, $messages);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), []);
        }

        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = User::where("id", $request->user)->where("ver_code", $request->otp)->first();
        if (Carbon::parse($auth_column->ver_code_send_at)->addSeconds($otp_exp_sec) < now()) {
            $this->authLogout($request);
            return Response::error([__('Session expired. Please try again')]);
        }
        $auth_column->update([
            'sms_verified'  => true,
        ]);
        $token = $auth_column->createToken("auth_token")->accessToken;
        return $this->authenticated($auth_column, $token, $request->type);
    }



    public function resendLoginCode(Request $request)
    {

        $rules = [
            'user'     => "required|exists:users,id",
        ];

        $messages = [
            'user.required'     => 'User id is required',
            'user.exists'       => 'User dose not exist'
        ];
        Validator::make($request->only(array_keys($rules)), $rules, $messages);

        $resend_code = User::where("id", $request->user)->first();
        if (!$resend_code) return Response::error([__('User is invalid')]);;
        if (Carbon::now() <= Carbon::parse($resend_code->ver_code_send_at)->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            return Response::error([__('You can resend verification code after ') . Carbon::now()->diffInSeconds(Carbon::parse($resend_code->ver_code_send_at)->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) . __(' seconds')], ['user' => $user, 'wait_time' => (string) Carbon::now()->diffInSeconds(Carbon::parse($resend_code->ver_code_send_at)->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE))], 400);
        }
        DB::beginTransaction();
        try {
            $update_data = [
                'code'          => generate_random_code(),
                'created_at'    => now(),
            ];
            DB::table('users')->where('id', $request->user)->update([
                'ver_code'          =>  $update_data['code'],
                'ver_code_send_at'  =>  $update_data['created_at'],
            ]);
            try {
                $message = __("Your verification code is: " . $update_data['code']);
                sendAuthSms($resend_code, $message);
            } catch (Exception $e) {
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return Response::error([__('Session expired. Please try again')]);
        }
        return Response::success([__('Verification code resend success!')], [
            'user_id' => $request->user,
            'wait_time' => ""
        ], 200);
    }
}
