@extends('layouts.master')

@push('css')
@endpush

@section('content')
    <section class="verification-otp pt-150 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class=" col-xl-6 col-lg-8 col-md-10 col-sm-12">
                    <div class="verification-otp-area">
                        <div class="account-wrapper otp-verification">
                            <div class="account-logo text-center">
                                <a href="{{ setRoute('frontend.index') }}" class="site-logo">
                                    <img src="{{ get_logo($basic_settings) }}"
                                        data-white_img="{{ get_logo($basic_settings, 'white') }}"
                                        data-dark_img="{{ get_logo($basic_settings, 'dark') }}" alt="logo">
                                </a>
                            </div>
                            <div class="verification-otp-content ptb-30">
                                <h4 class="title text-center">{{ __('Please enter the code') }}</h4>
                                <p class="d-block text-center">
                                    {{ __('We sent a 6 digit code your email') }}</strong>
                                </p>
                            </div>
                            <form class="account-form" action="{{ setRoute('user.password.forgot.verify.code', $token) }}"
                                method="POST">
                                @csrf
                                <div class="row ml-b-20">
                                    <div class="col-lg-12 form-group text-center">
                                        <input class="otp" type="text" maxlength="1" required name="otp[]">
                                        <input class="otp" type="text" maxlength="1" required name="otp[]">
                                        <input class="otp" type="text" maxlength="1" required name="otp[]">
                                        <input class="otp" type="text" maxlength="1" required name="otp[]">
                                        <input class="otp" type="text" maxlength="1" required name="otp[]">
                                        <input class="otp" type="text" maxlength="1" required name="otp[]">
                                    </div>
                                    <div class="col-lg-12 form-group ">
                                        <div class="time-area">{{ __('You can resend the code after') }} <span
                                                id="time"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 form-group text-center">
                                    <button type="submit" class="btn--base btn w-100">{{ __('Submit') }}</a>
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
    <script>
        var resendTime = "{{ $resend_time ?? 0 }}";
        var resendCodeLink = "{{ setRoute('user.password.forgot.resend.code', [$token,$mobile]) }}";

        function resetTime(second = 20) {
            var coundDownSec = second;
            var countDownDate = new Date();
            countDownDate.setMinutes(countDownDate.getMinutes() + 120);
            var x = setInterval(function() {
                var now = new Date().getTime();
                var distance = countDownDate -
                    now;
                var minutes = Math.floor((distance % (1000 * coundDownSec)) / (1000 * coundDownSec));
                var seconds = Math.floor((distance % (1000 * coundDownSec)) /
                    1000); 
                document.getElementById("time").innerHTML = second +
                    "s ";
                if (distance <= 0 || second <= 0) {
                    clearInterval(x);
                    document.querySelector(".time-area").innerHTML =
                        `Didn't get the code? <a class='text--danger' href='${resendCodeLink}'>Resend</a>`;
                }
                second--
            }, 1000);
        }
        resetTime(resendTime);
    </script>
    <script>
        const inputs = document.querySelectorAll('.otp');

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, ''); // digits only
                if (e.target.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                let pasteData = (e.clipboardData || window.clipboardData).getData('text');
                pasteData = pasteData.replace(/[^0-9]/g, ''); // digits only

                pasteData.split('').forEach((char, i) => {
                    if (inputs[index + i]) {
                        inputs[index + i].value = char;
                    }
                });

                let nextEmpty = [...inputs].find(inp => inp.value === '');
                if (nextEmpty) nextEmpty.focus();
            });
        });
    </script>
@endpush
