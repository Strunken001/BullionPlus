@extends('layouts.master')

@push('css')
@endpush

@section('content')
    <section class="account-section bg_img" data-background="{{ asset('frontend/images/element/account.png') }}">
        <div class="right float-end">
            <div class="account-header text-center">
                <a class="site-logo" href="{{ setroute('frontend.index') }}">
                    <img src="{{ get_logo($basic_settings) }}" data-white_img="{{ get_logo($basic_settings, 'white') }}"
                        data-dark_img="{{ get_logo($basic_settings, 'dark') }}" alt="logo">
                </a>
            </div>
            <div class="account-middle">
                <div class="account-form-area">
                    <h3 class="title">{{ __('Account Authorization') }}</h3>
                    <p>{{ __('Need to verify your account. Please check your mail inbox to get the authorization code') }}
                    </p>
                    <form action="{{ setRoute('user.authorize.mail.verify', $token) }}" class="account-form" method="POST">
                        @csrf
                        <div class="row ml-b-20">
                            <div class="col-lg-12 form-group">
                                @include('admin.components.form.input', [
                                    'name' => 'code',
                                    'placeholder' => 'Enter Verification Code',
                                    'required' => true,
                                    'value' => old('code'),
                                ])
                            </div>
                            <div class="col-lg-12 form-group">
                                <div class="forgot-item">
                                    <label>{{ __('Back to ') }}<a href="{{ setroute('frontend.index') }}"
                                            class="text--base">{{ __('Home') }}</a></label>
                                </div>
                            </div>
                            <div class="col-lg-12 form-group text-center">
                                <button type="submit" class="btn--base w-100">{{ __('Authorize') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="account-footer text-center">
                <p>{{ __('Copyright') }} © {{ date('Y', time()) }} {{ __('All Rights Reserved.') }}</a></p>
            </div>
        </div>
    </section>
@endsection

@push('script')
@endpush
