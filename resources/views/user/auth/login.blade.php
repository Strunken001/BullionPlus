@extends('layouts.master')

@push('css')
@endpush


@section('content')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            Start body overlay
                        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <div id="body-overlay" class="body-overlay"></div>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            End body overlay
                        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            Start Scroll-To-Top
                        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <a href="#" class="scrollToTop">
        <i class="las la-hand-point-up"></i>
        <small>{{ __('top') }}</small>
    </a>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            End Scroll-To-Top
                        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

    <section class="login-account-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-10 col-sm-12">
                    <div class="login-section-area">
                        <form action="{{ setRoute('user.login.submit') }}" class="account-form" method="POST">
                            @csrf
                            <div class="form-logo">
                                <a href="{{ setroute('frontend.index') }}">
                                    <img src="{{ get_logo($basic_settings) }}"
                                        data-white_img="{{ get_logo($basic_settings, 'white') }}"
                                        data-dark_img="{{ get_logo($basic_settings, 'dark') }}" alt="logo">
                                </a>
                            </div>
                            <div class="form-header">
                                <h3 class="title text-center">{{ __('Log in and Stay Connected') }}</h3>
                            </div>
                            <div class="access-btn">
                                <h4 class="title">{{ __('Login With') }}</h4>
                                <div class="switch-field">
                                    <input type="radio" id="radio-one" name="switch_one" value="OTP" checked />
                                    <label for="radio-one">{{ __('OTP') }}</label>
                                    <input type="radio" id="radio-two" name="switch_one" value="Password" />
                                    <label for="radio-two">{{ __('Password') }}</label>
                                </div>
                            </div>
                            <div class="otp-login-form" id="otp-login-form">
                                <div class="account-number country-code">
                                    <label>{{ __('Phone Number') }}:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select class="input-group-text copytext nice-select" name="otp_country">
                                                @foreach (get_all_countries_array() as $item)
                                                    <option value="{{ get_country_phone_code($item['name']) }}">
                                                        {{ $item['name'] }} ({{ $item['mobile_code'] }})</option>
                                                @endforeach
                                            </select>
                                            <input type="tel" type="number" class="form--control" name="otp_number"
                                                placeholder="Enter Number" id="phone-number">
                                        </div>
                                    </div>
                                </div>
                                <div class="custom-check-group pt-20">
                                    <input type="checkbox" id="level-112" name="stay">
                                    <label for="level-112">{{ __('Stay Login Next Time.') }}</label>
                                </div>
                                <div class="login-btn">
                                    <button type="submit" class="btn--base w-100">{{ __('Continue') }}</button>
                                </div>
                                @if ($basic_settings->user_registration)
                                    <div class="register-page">
                                        <div class="account-item">
                                            <label>{{ __("Don't Have An Account?") }} <a
                                                    href="{{ setRoute('user.register') }}"
                                                    class="account-control-btn">{{ __('Register Now') }}</a></label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="passwoard-login-area" id="password-login-area" style="display: none;">
                                <div class="account-number mb-20">
                                    <label>{{ __('Phone Number') }}:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select class="input-group-text copytext nice-select" name="pass_country"
                                                id="">
                                                @foreach (get_all_countries_array() as $item)
                                                    <option value="{{ get_country_phone_code($item['name']) }}">
                                                        {{ $item['name'] }} ({{ $item['mobile_code'] }})</option>
                                                @endforeach
                                            </select>
                                            <input type="tel" class="form--control" name="password_number"
                                            placeholder="Enter Number" value="123456789">
                                        </div>
                                    </div>
                                    <div class="account-passwoard show_hide_password mt-20">
                                        <label>{{ __('Password') }}:</label>
                                        <input type="password" class=" form--control" name="password"
                                            placeholder="Enter Password..." value="appdevs">
                                        <a href="#0" class="show-pass"><i class="fa fa-eye-slash"
                                                aria-hidden="true"></i></a>
                                    </div>
                                    <div class="forgot-item text-end">
                                        <span><a href="{{ setRoute('user.password.forgot') }}"
                                                class="text--base">{{ __('Forgot Password?') }}</a></span>
                                    </div>
                                    <div class="custom-check-group">
                                        <input type="checkbox" id="level-141" name="remember">
                                        <label for="level-141">{{ __('Remember Me') }}</label>
                                    </div>
                                    <div class="login-btn">
                                        <button type="submit" class="btn--base w-100">{{ __('Continue') }}</button>
                                    </div>
                                    @if ($basic_settings->user_registration)
                                        <div class="register-page">
                                            <div class="account-item">
                                                <label>{{ __("Don't Have An Account?") }} <a
                                                        href="{{ setRoute('user.register') }}"
                                                        class="account-control-btn">{{ __('Register Now') }}</a></label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        // Function to update the display based on the selected login option
        function updateLoginForm() {
            const otpForm = document.getElementById('otp-login-form');
            const passwordForm = document.getElementById('password-login-area');
            const otpRadio = document.getElementById('radio-one');

            if (otpRadio.checked) {
                otpForm.style.display = 'block';
                passwordForm.style.display = 'none';
            } else {
                otpForm.style.display = 'none';
                passwordForm.style.display = 'block';
            }
        }

        // Add event listeners to the radio buttons
        document.getElementById('radio-one').addEventListener('change', updateLoginForm);
        document.getElementById('radio-two').addEventListener('change', updateLoginForm);

        // Initial call to set the correct display on page load
        updateLoginForm();
    </script>
@endpush
