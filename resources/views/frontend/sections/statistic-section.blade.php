<section class="statistics-section ptb-80">
    <div class="container">
        <div class="row text-center">
            @forelse ($stats->value->items ?? [] as $value)
                <div class="col-lg-4 col-md-4 col-sm-6 pb-20">
                    <div class="counter">
                        <div class="icon">
                            <i class="{{ $value->icon }}"></i>
                        </div>
                        <div class="odo-area">
                            <h2 class="odo-title odometer" data-odometer-final="{{ $value->amount ?? ''}}">0</h2>
                        </div>
                        <h4 class="title">{{ __(@$value->language->$defualt->title ?? @$value->language->$default_lng->title) }}</h4>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    </div>
</section>
