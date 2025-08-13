@extends('layouts.master')

@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp

@push('css')
@endpush

@section('content')
    <section class="forgot-password pt-150 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-6 col-lg-7 col-md-10">
                    <div class="forgot-password-area">
                        <div class="account-wrapper">
                            <div class="account-logo text-center">
                                <a href="{{ setroute('frontend.index') }}" class="site-logo">
                                    <img src="{{ get_logo($basic_settings) }}"
                                        data-white_img="{{ get_logo($basic_settings, 'white') }}"
                                        data-dark_img="{{ get_logo($basic_settings, 'dark') }}" alt="logo">
                                </a>
                            </div>
                            <div class="forgot-password-content ptb-30">
                                <h3 class="title">{{ __('Reset Your Forgotten Password') }}?</h3>
                                <p>
                                    {{ __(@$forget_des->value->language->$defualt->forget_text) ?? '' }}
                                </p>
                            </div>
                            <form class="account-form" action="{{ setRoute('user.password.forgot.send.code') }}"
                                method="POST">
                                @csrf
                                <div class="row ml-b-20">
                                    <div class="col-lg-12 form-group text-center">
                                        <div class="account-number country-code">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <input type="text" required class="form--control" name="email"
                                                        placeholder="{{ __('Enter your email') }}" spellcheck="false"
                                                        data-ms-editor="true">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 form-group text-center">
                                        <button type="submit" class="btn--base btn w-100"> {{ __('Send OTP') }}</button>
                                    </div>
                                    <div class="col-lg-12 text-center">
                                        <div class="account-item">
                                            <label>{{ __('Already have an account?') }}
                                                <a href="{{ setRoute('user.login') }}" class="text--base">
                                                    {{ __('Login Now') }}
                                                </a>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
@endpush
