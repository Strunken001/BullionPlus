<div class="new-password pt-150 pb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-5">
                <div class="new-password-area">
                    <div class="account-wrapper">
                        <span class="account-cross-btn"></span>
                        <div class="account-logo text-center">
                            <a href="index.html" class="site-logo">
                                <img src="{{ asset('frontend/images/logo/web_logo.webp') }}" alt="logo">
                            </a>
                        </div>
                        <form class="account-form ptb-30" action="{{ setRoute('user.profile.password.update') }}" method="POST">
                            @method('PUT')
                            @csrf
                            <div class="row ml-b-20">
                                <label>{{ __('Enter Current Password') }}</label>
                                <div class="col-lg-12 form-group show_hide_password">
                                    <input type="password" name="current_password" class="form-control form--control"  placeholder="{{ __('Current Password') }}">
                                    <a href="javascript:void(0)" class="show-pass"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                </div>
                                <label>{{ __('Enter New Password') }}</label>
                                <div class="col-lg-12 form-group show_hide_password-2">
                                    <input type="password" name="password" class="form-control form--control"  placeholder="{{ __('Password') }}">
                                    <a href="javascript:void(0)" class="show-pass"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                </div>
                                <label>{{ __('Enter Confirmation Password') }}</label>
                                <div class="col-lg-12 form-group show_hide_password-2">
                                    <input type="password" name="password_confirmation" class="form-control form--control"  placeholder="{{ __('Confirmation Password') }}">
                                    <a href="javascript:void(0)" class="show-pass"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                </div>
                                <div class="col-lg-12 form-group text-center pt-3">
                                    <button type="submit" class="btn--base w-100">{{ __('Confirm') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
