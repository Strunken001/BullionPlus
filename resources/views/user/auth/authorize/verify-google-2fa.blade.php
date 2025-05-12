@extends('layouts.master')

@push('css')

@endpush

@section('content')
<section class="verification-otp ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class=" col-xl-6 col-lg-8 col-md-10 col-sm-12">
                <div class="verification-otp-area">
                    <div class="account-wrapper otp-verification">
                        <div class="account-logo text-center">
                            <a href="{{ setroute('frontend.index') }}" class="site-logo">
                                <img src="{{ get_logo($basic_settings) }}"
                                        data-white_img="{{ get_logo($basic_settings, 'white') }}"
                                        data-dark_img="{{ get_logo($basic_settings, 'dark') }}" alt="logo">
                            </a>
                        </div>
                        <div class="verification-otp-content pt-3">
                            <h3 class="title text-center">{{ __('OTP Verification') }}</h3>
                            <p class="d-block text-center">
                                {{ __('Please enter the') }}<strong>{{ __('6-digit') }}</strong>
                                {{ __('code from your app.') }}</p>
                        </div>
                        <form class="account-form pt-20"
                            action="{{ setRoute('user.authorize.google.2fa.submit') }}" method="POST">
                            @csrf
                            <div class="row ml-b-20">
                                <div class="col-lg-12 form-group text-center">
                                    <input class="otp" type="text" oninput='digitValidate(this)'
                                        onkeyup='tabChange(1)' maxlength=1 required name="otp[]">
                                    <input class="otp" type="text" oninput='digitValidate(this)'
                                        onkeyup='tabChange(2)' maxlength=1 required name="otp[]">
                                    <input class="otp" type="text" oninput='digitValidate(this)'
                                        onkeyup='tabChange(3)' maxlength=1 required name="otp[]">
                                    <input class="otp" type="text" oninput='digitValidate(this)'
                                        onkeyup='tabChange(4)' maxlength=1 required name="otp[]">
                                    <input class="otp" type="text" oninput='digitValidate(this)'
                                        onkeyup='tabChange(5)' maxlength=1 required name="otp[]">
                                    <input class="otp" type="text" oninput='digitValidate(this)'
                                        onkeyup='tabChange(6)' maxlength=1 required name="otp[]">
                                </div>
                                <div class="col-lg-12 form-group text-center">
                                    <button type="submit" class="btn--base btn w-100">{{ __('Submit') }}</button>
                                </div>
                            </div>
                            <div class="footer-text">
                                <p class="d-block text-center mt-3 create-acc">
                                    &mdash;
                                    <a href="{{ setroute('frontend.index') }}"
                                        class="text--base">{{ __('Back To') }}</a>
                                    &mdash;
                                </p>
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
<script>
    let digitValidate = function (ele) {
        console.log(ele.value);
        ele.value = ele.value.replace(/[^0-9]/g, '');
    }

    let tabChange = function (val) {
        let ele = document.querySelectorAll('.otp');
        if (ele[val - 1].value != '') {
            ele[val].focus()
        } else if (ele[val - 1].value == '') {
            ele[val - 2].focus()
        }
    }
</script>
@endpush
