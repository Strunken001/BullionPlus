<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\SiteSections;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Traits\User\LoggedInUsers;
use App\Traits\User\RegisteredUsers;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class RegisterController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, RegisteredUsers ,LoggedInUsers;

    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm() {
        $client_ip = request()->ip() ?? false;
        $user_country = geoip()->getLocation($client_ip)['country'] ?? "";
        $register_des = SiteSections::where('key','auth-section')->first();

        $page_title = setPageTitle("User Registration");
        return view('user.auth.register',compact(
            'page_title',
            'user_country',
            'register_des'
        ));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validated = $this->validator($request->all())->validate();

        $basic_settings             = $this->basic_settings;

        $validated = Arr::except($validated,['agree']);
        $validated['email_verified']        = ($basic_settings->email_verification == true) ? false : true;
        $validated['sms_verified']          = ($basic_settings->sms_verification == true) ? false : true;
        $validated['kyc_verified']          = ($basic_settings->kyc_verification == true) ? false : true;
        $validated['password']              = Hash::make($validated['password']);
        $validated['username']              = make_username($validated['firstname'],$validated['lastname']);
        $validated['address']['country']    = $validated['country'];
        $validated['mobile']                = remove_speacial_char($validated['mobile']);
        $validated['mobile_code']           = remove_speacial_char($validated['phone_code']);
        $complete_phone                     = $validated['mobile_code'] . $validated['mobile'];
        $validated['full_mobile']           = $complete_phone;
        $validated                          = Arr::except($validated,['agree','phone_code','phone']);
        // $validated['referral_id']       = generate_unique_string('users','referral_id',8,'number');0

        event(new Registered($user = $this->create($validated)));
        $this->guard()->login($user);

        return $this->registered($request, $user);
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data) {

        $basic_settings = $this->basic_settings;
        $password_rule = "required|string|min:6";
        if($basic_settings->secure_password) {
            $password_rule = ["required",Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()];
        }

        if($basic_settings->agree_policy){
            $agree = 'required|in:on';
        }else{
            $agree = 'nullable';
        }

        return Validator::make($data,[
            'firstname'     => 'required|string|max:60',
            'lastname'      => 'required|string|max:60',
            'email'         => 'required|string|email|max:150|unique:users,email',
            'mobile'        => 'required|string|max:20|unique:users,mobile',
            'phone_code'    => "required|string|max:20",
            'country'       => 'required|string',
            'password'      => $password_rule,
            'agree'         => $agree,
        ]);
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create($data);
    }


    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        try{
            $this->createUserWallets($user);
            $this->createLoginLog($user);
        }catch(Exception $e) {
            $this->guard()->logout();
            $user->delete();
            return redirect()->back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return redirect()->intended(route('user.dashboard'));
    }
}
