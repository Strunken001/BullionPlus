@extends('layouts.master')

@push('css')
@endpush

@section('content')
    <div id="body-overlay" class="body-overlay"></div>

    <a href="#" class="scrollToTop">
        <i class="las la-hand-point-up"></i>
        <small>{{ __('top') }}</small>
    </a>

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

                            <div class="passwoard-login-area">
                                <div class="account-number mb-20">
                                    <label>{{ __('Phone Number') }}:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select class="input-group-text copytext nice-select" name="pass_country">
                                                @foreach (get_all_countries_array() as $item)
                                                    <option value="{{ get_country_phone_code($item['name']) }}">
                                                        {{ $item['name'] }} ({{ $item['mobile_code'] }})</option>
                                                @endforeach
                                            </select>
                                            <input type="tel" class="form--control" name="password_number"
                                                placeholder="7000000000" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="account-passwoard show_hide_password mt-20">
                                        <label>{{ __('Password') }}:</label>
                                        <input type="password" class="form--control" name="password"
                                            placeholder="Enter Password..." autocomplete="off">
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
                                        <button type="submit" class="btn--base w-100" id="login-btn" data-loading-text="{{ __('Please wait...') }}">
                                            {{ __('Continue') }}
                                        </button>
                                    </div>

                                    @if ($basic_settings->user_registration)
                                        <div class="register-page">
                                            <div class="account-item">
                                                <label>{{ __("Don't Have An Account?") }}
                                                    <a href="{{ setRoute('user.register') }}"
                                                        class="account-control-btn">{{ __('Register Now') }}</a>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
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
        document.addEventListener('DOMContentLoaded', function () {
            const phoneInput = document.querySelector('input[name="password_number"]');
            if (phoneInput) {
                phoneInput.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            const loginForm = document.querySelector('.account-form');
            const loginBtn = document.getElementById('login-btn');

            if (loginForm && loginBtn) {
                loginForm.addEventListener('submit', function () {
                    loginBtn.disabled = true;
                    const loadingText = loginBtn.getAttribute('data-loading-text') || 'Loading...';
                    loginBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ${loadingText}`;
                });
            }
        });
    </script>
@endpush

@push('css')
<style>
    .spinner-border {
        width: 1rem;
        height: 1rem;
        vertical-align: text-bottom;
        margin-right: 5px;
    }
</style>
@endpush
