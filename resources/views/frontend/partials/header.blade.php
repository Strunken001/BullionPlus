@php
    $current_url = URL::current();
    $pages = App\Models\Admin\SetupPage::where(['status' => true])->orWhere('slug',"home")->get();
@endphp
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<header class="header-section two">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container custom-container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-lg p-0">
                        <a class="site-logo site-title" href="{{ setRoute('frontend.index') }}">
                            <img src="{{ get_logo($basic_settings) }}"
                                data-white_img="{{ get_logo($basic_settings, 'white') }}"
                                data-dark_img="{{ get_logo($basic_settings, 'dark') }}" alt="site-logo">
                        </a>
                        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ms-auto">
                                @php
                                    $current_url = URL::current();
                                @endphp
                                @foreach ($pages as $item)
                                    @php
                                        $title = json_decode($item->title);
                                    @endphp
                                    <li>
                                        <a href="{{ url($item->url) }}" class="@if ($current_url == url($item->url)) active @endif">
                                            <span>{{ __($item->title) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="header-language">
                                @php
                                    $session_lan = session('local')??get_default_language_code();
                                @endphp
                                <div class="language-select">
                                    <select class="form--control nice-select" name="lang_switcher">
                                        @foreach ($__languages as $item)
                                            <option value="{{ $item->code }}" @if($session_lan == $item->code)
                                                @selected(true)
                                            @endif>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if (Auth::check())
                                <div class="header-action dashboard-dropdown-btn">
                                    @if (auth()->user()->image)
                                        <a href="javascript:void(0);" class="profile-action-btn">
                                            <img src="{{ get_image(auth()->user()->image, 'user-profile') }}"
                                                alt="img">
                                        </a>
                                    @else
                                        <a href="javascript:void(0);" class="profile-action-btn">
                                            <img src="{{ asset('public/frontend/images/default/profile-default.webp') }}"
                                                alt="img">
                                        </a>
                                    @endif
                                    <div class="dropdown-btn-list">
                                        <ul class="dropdown-list-item">
                                            <li>
                                                <div class="title-area">
                                                    <a href="{{ setRoute('user.dashboard') }}">{{ __('Dashboard') }}</a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="title-area">
                                                    <a href="{{ setRoute('user.profile.index') }}">{{ __('My Profile') }}</a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="title-area">
                                                    <a href="{{ setRoute('user.profile.password') }}">{{ __('Change Password') }}</a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="title-area">
                                                    <a href="{{ setRoute('user.gift.card.index') }}">{{ __('Buy Giftcard') }}</a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="title-area">
                                                    <a href="{{ setRoute('user.mobile.topup.automatic.index') }}">{{ __('Mobile Topup') }}</a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="title-area">
                                                    <a href="{{ setRoute('user.security.google.2fa') }}">{{ __('2FA Verification') }}</a>
                                                </div>
                                            </li>
                                            @if ($basic_settings->kyc_verification)
                                                <li>
                                                    <div class="title-area">
                                                        <a
                                                            href="{{ setRoute('user.kyc.index') }}">{{ __('KYC Verification') }}</a>
                                                    </div>
                                                </li>
                                            @endif
                                            <li>
                                                <div class="title-area">
                                                    <a href="{{ setRoute('user.support.ticket.index') }}">{{ __('Support') }}</a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="title-area">
                                                    <a class="logout-btn"
                                                        href="javascript:void(0)">{{ __('Logout') }}</a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <div class="header-action">
                                    <a href="{{ setRoute('user.login') }}" class="btn--base">{{ __('Login Now') }}</a>
                                </div>
                            @endif
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@push('script')
    <script>
        $(".logout-btn").click(function() {
            var actionRoute = "{{ setRoute('user.logout') }}";
            var target = 1;
            var message = `{{ __('Are you sure to') }} <strong>{{ __('Logout') }}</strong>?`;
            openAlertModal(actionRoute, target, message, "{{ __('Logout') }}", "POST");
        });
    </script>
@endpush
