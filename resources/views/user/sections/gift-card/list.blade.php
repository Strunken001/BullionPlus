<?php
$defualt = get_default_language_code() ?? 'en';
$default_lng = 'en';
?>
@extends('user.layouts.master')
@section('breadcrumb')
    @include('user.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('user.dashboard'),
            ],
        ],
        'active' => __(),
    ])
@endsection
@section('content')
    <section class="gift-card-section ptb-80">
        <div class="container">
            <div class="section-title">
                <div class="row">
                    <div class="col-xl-8 col-lg-10">
                        <h2 class="title">{{ __('Send Gift Card') }}</h2>
                        <p>{{ $section_data->value->language->$defualt->description ?? $section_data->value->language->$default_lng->description }}
                        </p>
                    </div>
                </div>
                <div class="searching-giftcard mt-20">
                    <p>{{ __("Choose recipient's country to select gift cards.") }}</p>
                    <div class="select-area">
                        <form action="{{ setRoute('user.gift.card.search') }}" method="GET">
                            <div class="row mb-20-none">
                                <div class="col-xl-4 col-lg-12 col-md-12 mb-20">
                                    <select class="select2-auto-tokenize" name="country">
                                        <option selected>{{__('Choose a country')}}</option>
                                        @foreach (get_all_countries(global_const()::USER) ?? [] as $country)
                                            <option value="{{ $country->iso2 }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-2 col-lg-4 col-md-4 mb-20">
                                    <div class="search-btn">
                                        <button type="submit" class="btn--base w-100">{{ __('Search Now') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="choice-gitcard-area pt-40">
                        <div class="row justify-content-center mb-20-none">
                            @forelse ($products ??[] as $key => $card)
                                @php
                                    $image = $card['logoUrls'][0];
                                @endphp
                                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 mb-20">
                                    <a href="{{ setRoute('user.gift.card.details', $card['productId']) }}">
                                        <div class="gift-card-img">
                                            <img src="{{ $image ?? '' }}" alt="card">
                                        </div>
                                        <div class="gift-card-content">
                                            <h5 class="title"><a
                                                    href=" {{ setRoute('user.gift.card.details', $card['productId']) }}">{{ $card['productName'] }}</a>
                                            </h5>
                                        </div>
                                    </a>
                                </div>
                            @empty
                            @endforelse
                            @if (count($products ?? []) > 0)
                                {{ $products->withQueryString()->setPath(url()->current())->links('pagination::bootstrap-5') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
@endpush
