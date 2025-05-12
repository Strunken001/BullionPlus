<section class="faq-section pb-80">
    <div class="container">
        <div class="section-header-area mb-40">
            <div class="row">
                <div class="col-xl-8 col-lg-10 col-md-12">
                    <div class="section-tag text-start">
                        <span>
                            <img src="{{ get_fav($basic_settings) }}" data-white_img="{{ get_fav($basic_settings, 'white') }}"
                            data-dark_img="{{ get_fav($basic_settings, 'dark') }}" alt="logo">
                            {{ __('Frequently Asked Question') }}
                        </span>
                    </div>
                    <div class="section-title pb-20">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2 class="title">{{ __(@$faq->value->language->$defualt->heading ?? @$faq->value->language->$default_lng->heading) }}</h2>
                                <p>{{ __(@$faq->value->language->$defualt->sub_heading ?? @$faq->value->language->$default_lng->sub_heading) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-20-none align-items-center">
            <div class="col-lg-6 mb-20">
                <div class="faq-img">
                    <img src="{{ get_image($faq->value->image, 'site-section') }}" alt="img">
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 mb-20">
                <div class="faq-wrapper">
                    @forelse ($faq->value->items ?? [] as $value)
                        <div class="faq-item">
                            <h3 class="faq-title"><span class="title">{{ __(@$value->language->$defualt->question ?? @$value->language->$default_lng->question) }}</span><span
                                    class="right-icon"></span></h3>
                            <div class="faq-content">
                                <p>{{ __(@$value->language->$defualt->answer ?? @$value->language->$default_lng->answer) }}</p>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
