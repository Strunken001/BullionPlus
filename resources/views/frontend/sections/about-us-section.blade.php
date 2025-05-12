<section class="about-section ptb-80">
    <div class="container">
        <div class="row mb-30-none">
            <div class="col-lg-6 mb-30">
                <div class="about-area">
                    <div class="section-tag text-start">
                        <span> <img src="{{ get_fav($basic_settings) }}" data-white_img="{{ get_fav($basic_settings, 'white') }}"
                            data-dark_img="{{ get_fav($basic_settings, 'dark') }}" alt="logo"> {{ __('About Us') }}</span>
                    </div>
                    <div class="about-details">
                        <h2 class="title">{{ __(@$about_us->value->language->$defualt->heading ?? @$about_us->value->language->$default_lng->heading) }}</h2>
                        <p>{{ __(@$about_us->value->language->$defualt->sub_heading ?? @$about_us->value->language->$default_lng->sub_heading) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-30">
                <div class="about-img">
                    <img src="{{ get_image($about_us->value->image, "site-section") }}" alt="img">
                </div>
            </div>
        </div>
    </div>
</section>
