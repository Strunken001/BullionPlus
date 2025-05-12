<div class="banner-slider">
    <div class="swiper-wrapper">
        @forelse ($banner->value->items ?? [] as $value)
            <div class="swiper-slide">
                <div class="banner-section">
                    <div class="container">
                        <div class="row align-items-center mb-30-none">
                            <div class="col-xl-5 col-lg-8 mb-30">
                                <div class="banner-content">
                                    <h1 class="title">{{ __(@$value->language->$defualt->heading ?? @$value->language->$default_lng->heading) }}</h1>
                                    <p>{{ __(@$value->language->$defualt->sub_heading ?? @$value->language->$default_lng->sub_heading) }}</p>
                                    <div class="banner-btn mt-40">
                                        <a href="{{ url($value->button_link ?? '') }}"
                                            class="btn--base">{{ __(@$value->language->$defualt->button_name ?? @$value->language->$default_lng->button_name) }}
                                            <i class="las la-redo"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-4 mb-30">
                                <div class="banner-img">
                                    <img src="{{ get_image($value->image, 'site-section') }}" alt="img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
        @endforelse
    </div>
    <div class="slider-prev slider-nav">
        <i class="las la-angle-left"></i>
    </div>
    <div class="slider-next slider-nav">
        <i class="las la-angle-right"></i>
    </div>
</div>
