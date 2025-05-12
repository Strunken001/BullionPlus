<!-- service Section -->
<section class="service-section ptb-80">
    <div class="container">
        <div class="section-tag text-start">
            <span>
                <img src="{{ get_fav($basic_settings) }}" data-white_img="{{ get_fav($basic_settings, 'white') }}"
                data-dark_img="{{ get_fav($basic_settings, 'dark') }}" alt="logo">
                {{ __('Our Services') }}
            </span>
        </div>
        <div class="service-title">
            <div class="row">
                <div class="col-xl-8 col-lg-10 col-sm-12">
                    <h2 class="title">{{ __(@$services->value->language->$defualt->heading ?? @$services->value->language->$default_lng->heading) }}</h2>
                    <p>{{ __(@$services->value->language->$defualt->sub_heading ?? @$services->value->language->$default_lng->sub_heading) }}</p>
                </div>
            </div>
        </div>
         <div class="service-item-group pt-60">
             <div class="row mb-20-none">
                @forelse ($services->value->items ?? [] as $value)
                <div class="col-lg-4 col-md-6 mb-20">
                    <div class="service-item-area">
                        <a href="{{ setRoute(@$value->link) }}">
                           <div class="service-icon">
                               <div class="plan-item">
                                   <img src="{{ get_image($value->image, "site-section") }}" alt="icon">
                               </div>
                               <div class="service-details">
                                   <h3 class="title">{{ __(@$value->language->$defualt->title ?? @$value->language->$default_lng->title) }}</h3>
                                   <p>{{ __(@$value->language->$defualt->description ?? @$value->language->$default_lng->description) }}</p>
                               </div>
                           </div>
                        </a>
                    </div>
                </div>
                @empty

                @endforelse
             </div>
         </div>
    </div>
</section>
