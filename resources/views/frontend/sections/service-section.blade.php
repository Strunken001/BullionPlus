<section class="project-plan  ptb-40">
    <div class="container">
        <div class="row text-center">
            <div class="plan-slider-wrapper">
                <div class="plan-slider">
                    <div class="swiper-wrapper">
                        @forelse ($services->value->items ?? [] as $value)
                            <div class="swiper-slide">
                                <a href="{{ setRoute(@$value->link) }}">
                                    <div class="plan-item">
                                        <img src="{{ get_image($value->image, "site-section") }}" alt="icon">
                                    </div>
                                    <P>{{ __(@$value->language->$defualt->title ??  @$value->language->$default_lng->title) }}</P>
                                </a>
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
            </div>
        </div>
    </div>
</section>
