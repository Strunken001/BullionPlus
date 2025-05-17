<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Footer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<footer class="footer-section bg-overlay bg_img"
    data-background="{{ asset('public/frontend/images/element/footer-bg.webp') }}">
    <div class="container">
        <div class="footer-area">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8 col-md-10">
                    <div class="footer-logo">
                        <a class="site-logo site-title" href="{{ setRoute('frontend.index') }}">
                            <img src="{{ get_logo($basic_settings) }}"  data-white_img="{{ get_logo($basic_settings,'white') }}"
                            data-dark_img="{{ get_logo($basic_settings,'dark') }}" alt="site-logo">
                        </a>
                    </div>
                    <div class="footer-social-icon">
                        @forelse ($footer->value->contact->social_links ?? [] as $value)
                            <a href="{{ $value->link }}"><i class="{{ $value->icon }}"></i></a>
                        @empty
                        @endforelse
                    </div>
                    <div class="footer-content">
                        <p>{{ __(@$footer->value->language->$defualt->footer_desc ?? @$footer->value->language->$default_lng->footer_desc) }}</p>
                    </div>
                </div>
            </div>
            <div class="copyright-area">
                <div class="left-side">
                    <div class="copyright-text">
                        <p>{{ __('Copyright') }} &copy; {{ __('2024') }}, {{ __('All Right Reserved') }} <a
                                href="{{ setRoute('frontend.index') }}"><span>{{ $basic_settings->site_name }}</span></a>
                        </p>
                    </div>
                </div>
                <div class="right-side">
                    <div class="page-link-item">
                        @forelse (getLinks() ?? [] as $item)
                        <li><a href="{{ setRoute('global.usefull.page',$item->slug) }}">{{ ($item->title->language->$defualt->title ?? $item->title->language->$default_lng->title ) }}</a></li>
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Footer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start cookie
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<div class="cookie-main-wrapper">
    <div class="cookie-content">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M21.598 11.064a1.006 1.006 0 0 0-.854-.172A2.938 2.938 0 0 1 20 11c-1.654 0-3-1.346-3.003-2.937c.005-.034.016-.136.017-.17a.998.998 0 0 0-1.254-1.006A2.963 2.963 0 0 1 15 7c-1.654 0-3-1.346-3-3c0-.217.031-.444.099-.716a1 1 0 0 0-1.067-1.236A9.956 9.956 0 0 0 2 12c0 5.514 4.486 10 10 10s10-4.486 10-10c0-.049-.003-.097-.007-.16a1.004 1.004 0 0 0-.395-.776zM12 20c-4.411 0-8-3.589-8-8a7.962 7.962 0 0 1 6.006-7.75A5.006 5.006 0 0 0 15 9l.101-.001a5.007 5.007 0 0 0 4.837 4C19.444 16.941 16.073 20 12 20z"/><circle cx="12.5" cy="11.5" r="1.5"/><circle cx="8.5" cy="8.5" r="1.5"/><circle cx="7.5" cy="12.5" r="1.5"/><circle cx="15.5" cy="15.5" r="1.5"/><circle cx="10.5" cy="16.5" r="1.5"/></svg>
        <p class="text-white">{{ __(strip_tags(@$cookie->value->desc)) }} <a href="{{ url('/').'/'.@$cookie->value->link }}">{{ __("Privacy Policy") }}</a></p>
    </div>
    <div class="cookie-btn-area">
        <button class="cookie-btn">{{__("Allow")}}</button>
        <button class="cookie-btn-cross">{{__("Decline")}}</button>
    </div>
</div>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End cookie
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
