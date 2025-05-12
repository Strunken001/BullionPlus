<section class="security-system ptb-80">
    <div class="container">
        <div class="section-tag text-start">
            <span> <img src="{{ get_fav($basic_settings) }}" data-white_img="{{ get_fav($basic_settings, 'white') }}"
                data-dark_img="{{ get_fav($basic_settings, 'dark') }}" alt="logo">{{ __('Security System') }}</span>
        </div>
        <div class="section-header-area">
            <div class="row">
                <div class="col-xl-8 col-lg-10 col-md-12">
                    <div class="section-title">
                        <h2 class="title">{{ __(@$securities->value->language->$defualt->heading ?? @$securities->value->language->$default_lng->heading) }}</h2>
                    </div>
                    <div class="section-sub-title">
                        <p>{{ __(@$securities->value->language->$defualt->sub_heading ?? @$securities->value->language->$default_lng->sub_heading) }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center pt-40">
            @forelse (@$securities->value->items ?? [] as $value)
                <div class="col-xl-4 col-lg-6 col-md-6 pb-20">
                    <div class="security-item">
                        <span class="icon"><i class="{{ @$value->icon }}"></i></span>
                        <div class="security-content">
                            <h4 class="title">{{ __(@$value->language->$defualt->title ?? @$value->language->$default_lng->title) }}<span>{{ __(@$value->language->$defualt->highlighted_title ??  @$value->language->$default_lng->highlighted_title) }}</span></h4>
                            <p>{{ __(@$value->language->$defualt->description ?? @$value->language->$default_lng->description) }}</p>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    </div>
</section>
