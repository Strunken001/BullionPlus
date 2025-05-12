<section class="app-section ptb-80">
    <div class="container">
        <div class="section-tag text-start">
            <span> <img src="{{ get_fav($basic_settings) }}" data-white_img="{{ get_fav($basic_settings, 'white') }}"
                data-dark_img="{{ get_fav($basic_settings, 'dark') }}" alt="logo">{{ __('Download App') }}</span>
        </div>
        <div class="app-section-title pb-30">
            <div class="row">
                <div class="col-xl-8 col-lg-10 col-md-12">
                    <h2 class="title">{{ __(@$downloads->value->language->$defualt->heading ?? @$downloads->value->language->$defualt_lng->heading) }}</h2>
                </div>
            </div>
        </div>
        <div class="row mb-30-none">
            <div class="col-lg-6 mb-30">
                <div class="app-area-content">
                    <p>{{ __(@$downloads->value->language->$defualt->sub_heading ?? @$downloads->value->language->$defualt_lng->sub_heading) }}</p>
                </div>
                <div class="row mb-30-none">
                    @forelse ($downloads->value->items ?? [] as $value)
                        <div class="col-xl-6 col-lg-6 mb-30">
                            <div class="app-btn-wrapper">
                                <a href="#0" class="app-btn">
                                    <div class="app-icon">
                                        <i class="{{ __(@$value->icon ?? '') }}"></i>
                                    </div>
                                    <div class="content">
                                        <span>{{ __('Get It On') }}</span>
                                        <h5 class="title">{{ __(@$value->language->$defualt->title ?? '') }}</h5>
                                    </div>
                                    <div class="icon">
                                        <img src="https://qrcode.tec-it.com/API/QRCode?data={{ @$value->link }}" alt="element">
                                    </div>
                                    <div class="app-qr">
                                        <img src="https://qrcode.tec-it.com/API/QRCode?data={{ @$value->link }}" alt="element">
                                    </div>
                                </a>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
            <div class="col-lg-6 mb-30">
                <div class="app-img">
                    <img src="{{ get_image($downloads->value->image, "site-section") }}" alt="img">
                </div>
            </div>
        </div>
    </div>
</section>
