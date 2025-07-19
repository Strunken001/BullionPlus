@extends('layouts.master')
@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp

@push('css')
@endpush

@section('content')
    <section class="login-account-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-10 col-sm-12">
                    <div class="login-section-area">
                        <form action="{{ setRoute('user.register.submit') }}" class="account-form" method="POST">
                            @csrf
                            <div class="form-logo">
                                <a href="{{ route('frontend.index') }}">
                                    <img src="{{ get_logo($basic_settings) }}"
                                        data-white_img="{{ get_logo($basic_settings, 'white') }}"
                                        data-dark_img="{{ get_logo($basic_settings, 'dark') }}" alt="logo">
                                </a>
                            </div>
                            <div class="form-header text-center">
                                <h3 class="title">{{ __('Register for an Account Today') }}</h3>
                                <p>
                                    {{ __(@$register_des->value->language->$defualt->register_text) ?? '' }}
                                </p>
                            </div>
                            <div class="form-body mt-20">
                                <div class="row mb-10-none">
                                    <div class="col-lg-6 col-md-6 mb-10">
                                        <label>{{ __('First Name') }}</label>
                                        <input type="text" class="form-control form--control" name="firstname"
                                            placeholder="{{ __('First Name') }}" value="{{ old('firstname') }}">
                                    </div>
                                    <div class="col-lg-6 col-md-6 mb-10">
                                        <label>{{ __('Last Name') }}</label>
                                        <input type="text" class="form-control form--control" name="lastname"
                                            value="{{ old('lastname') }}" placeholder="{{ __('Last Name') }}">
                                    </div>
                                    <div class="col-12 mb-10">
                                        <label>{{ __('Select Country') }}</label>
                                        <select class="form--control select-2 select2-auto-tokenize country-select"
                                            data-placeholder="{{ __('Select Country') }}" name="country"
                                            data-old="{{ old('country', auth()->user()->address->country ?? '') }}">
                                            <option selected disabled>Loading...</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-10">
                                        <label>{{ __('Mobile') }}</label>
                                        <div class="input-group">
                                            <div class="input-group-text phone-code">+</div>
                                            <input class="phone-code" type="hidden" name="phone_code"
                                                value="{{ old('phone_code') }}" />
                                            <input type="text" class="form--control"
                                                placeholder="{{ __('Enter Phone Number') }}" name="mobile"
                                                value="{{ old('mobile') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 mb-10">
                                        <label>{{ __('Email Address') }}</label>
                                        <input type="email" class="form-control form--control" name="email"
                                            placeholder="{{ __('Email') }}" value="{{ old('email') }}">
                                    </div>
                                    <div class="col-12 mb-10 register-passwoard">
                                        <div class="show_hide_password">
                                            <label>{{ __('Enter Password') }}</label>
                                            <input type="password" class=" form--control" name="password"
                                                placeholder="{{ __('Enter Password') }}...">
                                            <a href="#0" class="show-pass"><i class="fa fa-eye-slash"
                                                    aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                    @if ($basic_settings->agree_policy)
                                        <div class="col-lg-12 form-group">
                                            <div class="custom-check-group">
                                                <input type="checkbox" id="level-1" name="agree">
                                                <label for="level-1">{{ __('I have agreed with') }} <a
                                                        href="{{ route('global.usefull.page', 'refund-policy') }}"
                                                        target="_blank">{{ __('Terms Of Use') }}</a> & <a
                                                        href="{{ route('global.usefull.page', 'privacy-policy') }}"
                                                        target="_blank">{{ __('Privacy Policy') }}</a></label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="register-btn pt-4">
                                    <button type="submit" class="btn--base w-100">{{ __('Register Now') }}</button>
                                </div>
                                <div class="register-page">
                                    <div class="account-item">
                                        <label>{{ __('Already Have An Account?') }} <a href="{{ setRoute('user.login') }}"
                                                class="account-control-btn">{{ __('Login Now') }}</a></label>
                                    </div>
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
        getAllCountries("{{ setRoute('global.countries') }}");
        $(document).ready(function() {
            $("select[name=country]").change(function() {
                var phoneCode = $("select[name=country] :selected").attr("data-mobile-code");
                placePhoneCode(phoneCode);
            });

            countrySelect(".country-select", $(".country-select").siblings(".select2"));
            stateSelect(".state-select", $(".state-select").siblings(".select2"));
        });
    </script>
@endpush
