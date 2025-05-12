<section class="how-it-work ptb-80">
    <div class="container">
        <div class="section-tag text-start">
            <span>
                <img src="{{ get_fav($basic_settings) }}" data-white_img="{{ get_fav($basic_settings, 'white') }}"
                data-dark_img="{{ get_fav($basic_settings, 'dark') }}" alt="logo">
                {{ __('How It Works') }}
            </span>
        </div>
        <div class="section-header-area mb-40">
            <div class="row">
                <div class="col-xl-8 col-lg-10 col-md-12">
                    <div class="section-title">
                        <h2 class="title">{{ __(@$how_it_works->value->language->$defualt->heading ?? @$how_it_works->value->language->$default_lng->heading) }}</h2>
                    </div>
                    <div class="section-sub-title">
                        <p>{{ __(@$how_it_works->value->language->$defualt->sub_heading ?? @$how_it_works->value->language->$default_lng->sub_heading) }}.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-30-none">
            <div class="col-xl-6 col-lg-12 mb-30">
                <div class="how-it-work-img">
                    <img src="{{ get_image(@$how_it_works->value->image, 'site-section') }}" alt="img">
                </div>
            </div>
            <div class="col-xl-6 col-lg-12 mb-30">
                <div class="how-it-work-content">
                    <div class="row align-items-center">
                        @forelse ($how_it_works->value->items ?? [] as $value)
                            <div class="col-12 mb-20">
                                <div class="working-list" data-aos="fade-left" data-aos-duration="1200">
                                    <div class="number">
                                        <h3 class="title">{{ $loop->iteration }}.</h3>
                                    </div>
                                    <div class="work-content tri-right left-top">
                                        <p>{{ @$value->language->$defualt->description ?? @$value->language->$default_lng->description}}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
