<!-- blog section -->
<section class="blog-section blog-details-section ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-7">
                <div class="blog-item">
                    <div class="blog-thumb">
                        <img src="{{ get_image(@$blog->data->image, 'site-section') }}" alt="blog">
                    </div>
                    <div class="blog-content pt-3=4">
                        <h3 class="title">{{ __(@$blog->data->language->$defualt->title ?? @$blog->data->language->$default_lng->title) }}</h3>
                        <p>
                            <?= @$blog->data->language->$defualt->description ?? @$blog->data->language->$default_lng->description ?>
                        </p>
                        <div class="blog-tag-wrapper">
                            {{-- <span>{{ __('tags') }}</span> --}}
                            <?php $tags = ($blog->data?->language?->$defualt?->tags ?? $blog->data?->language?->$default_lng?->tags) ?? []; ?>
                            <ul class="blog-footer-tag">
                                @forelse ($tags ?? [] as $value)
                                    <li><a href="#0">{{ $value }}</a></li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5 mb-30">
                <div class="blog-sidebar">
                    <div class="widget-box mb-30">
                        <h4 class="widget-title">{{ __('Categories') }}</h4>
                        <div class="category-widget-box">
                            <ul class="category-list">
                                @forelse ($categories ?? [] as $value)
                                    <li>
                                        <a href="{{ setRoute('frontend.blog.category.view',$value->id) }}">{{ __(@$value->name->language->$defualt->name ?? @$value->name->language->$default_lng->name) }}
                                            <span>{{ blogCount($value->id) }}</span>
                                        </a>
                                    </li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div class="widget-box mb-30">
                        <h4 class="widget-title">{{ __('Recent Posts') }}</h4>
                        <div class="popular-widget-box">
                            @forelse ($recent_blogs ?? [] as $value)
                                <div class="single-popular-item d-flex flex-wrap align-items-center">
                                    <div class="popular-item-thumb">
                                        <a href="{{ setRoute('frontend.blog.view', $value->id) }}">
                                            <img src="{{ get_image(@$value->data->image, 'site-section') }}"
                                                alt="blog">
                                        </a>
                                    </div>
                                    <div class="popular-item-content">
                                        <span class="date">{{ __(showDate($value->created_at)) }}</span>
                                        <h6 class="title"><a
                                                href="{{ setRoute('frontend.blog.view', $value->id) }}">{{ __(@$value->data->language->$defualt->title ?? @$value->data->language->$default_lng->title) }}</a>
                                        </h6>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
