<!-- category blog section -->
<section class="blog-section  ptb-80">
    <div class="container">
        <div class="blog-title">
            <div class="row">
                <div class="col-xl-8 col-lg-10 col-sm-12">
                    <div class="section-tag text-start">
                        <span>
                            <img src="{{ get_fav($basic_settings) }}"
                                data-white_img="{{ get_fav($basic_settings, 'white') }}"
                                data-dark_img="{{ get_fav($basic_settings, 'dark') }}" alt="logo">
                            {{ __('Web Journal') }}
                        </span>
                    </div>
                    {{-- <h2 class="title">{{ __(@$blog->value->language->$defualt->heading ?? '') }}</h2>
                    <p>{{ __(@$blog->value->language->$defualt->sub_heading ?? '') }}</p> --}}
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            @forelse ($category_blogs ?? [] as $value)
                <div class="col-xl-4 col-lg-6 col-md-6 pb-20">
                    <a href="{{ setRoute('frontend.blog.view',$value->id) }}">
                        <div class="blog-area">
                            <div class="blog-img">
                                <img src="{{ get_image($value->data->image, "site-section") }}" alt="img">
                            </div>
                            <div class="blog-content">
                                <div class="time-date">
                                    <div class="date">
                                        <i class="las la-calendar-alt"></i>
                                        <p>{{ __($value->created_at->toDateString()) }} </p>
                                    </div>
                                    <div class="time">
                                        <i class="las la-clock"></i>
                                        <p>{{ __($value->created_at->toTimeString()) }} </p>
                                    </div>
                                </div>
                                <h3 class="content-title">{{ __(@$value->data->language->$defualt->title ?? '') }}</h3>
                                <p><?= __(Str::limit(@$value->data->language->$defualt->description, 10, '...')) ?></p>
                            </div>
                            <div class="blog-btn">
                                <a href="{{ setRoute('frontend.blog.view',$value->id) }}" class="btn--base w-100">{{ __('About More') }}</a>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
            @endforelse
        </div>
    </div>
</section>
