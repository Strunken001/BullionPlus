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
                            <img src="{{ get_logo($basic_settings) }}"
                                data-white_img="{{ get_logo($basic_settings, 'white') }}"
                                data-dark_img="{{ get_logo($basic_settings, 'dark') }}" alt="site-logo">
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
                        <p>{{ __('Copyright') }} &copy; {{ "2024" }},{{ __('All Right Reserved') }} <a
                                href="{{ setRoute('frontend.index') }}"><span>{{ __('Payload') }}</span></a>
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
