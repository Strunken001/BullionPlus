<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Flexiplan preview
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="flexiplan-preview-section ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9 col-sm-12">
                <div class="get-package-area">
                    <a href="{{ url()->previous() }}" class="text--base d-inline-block mb-3">
                        <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                    </a>
                    <h3 class="title"><i class="fas fa-info-circle text--base mb-20"></i>{{ __('Your Bundle') }}</h3>
                    <div class="plan-preview">
                        <div class="plan-name">
                            <span class="badge badge--base">{{ __('Operator Name') }}</span>
                        </div>
                        <div class="plan-quantity">
                            <span>{{ $charges['operator_name'] }}</span>
                        </div>
                    </div>
                    <div class="plan-preview">
                        <div class="plan-name">
                            <span class="badge badge--base">{{ __('Bundle') }}</span>
                        </div>
                        <div class="plan-quantity">
                            <span>{{ $info['name'] }}</span>
                        </div>
                    </div>
                    <div class="plan-preview">
                        <div class="plan-name">
                            <span class="badge badge--base">{{ __('Price') }}</span>
                        </div>
                        <div class="plan-quantity">
                            <span>{{ $info['amount'] }} {{ $charges['bundle_currency'] }}</span>
                        </div>
                    </div>
                    <div class="plan-preview">
                        <div class="plan-name">
                            <span class="badge badge--base">{{ __('Exchange Rate') }}</span>
                        </div>
                        <div class="plan-quantity">
                            <span>1 {{ $charges['bundle_currency'] }} = {{ $charges['exchange_rate'] }} {{ $charges['wallet_currency_code'] }}</span>
                        </div>
                    </div>
                    <div class="plan-preview">
                        <div class="plan-name">
                            <span class="badge badge--base">{{ __('Total Payable') }}</span>
                        </div>
                        <div class="plan-quantity">
                            <span>{{ $info['amount'] * $charges['exchange_rate'] }} {{ $charges['wallet_currency_code'] }}</span>
                        </div>
                    </div>
                    <form action="{{ setRoute('user.data.bundle.buy') }}" method="POST">
                        @csrf
                        <div class="package-number pt-20">
                            <div class="package-select">
                                <label>{{ __('For Number') }} :</label>
                            </div>
                            <input type="number" name="phone" id="number-input" class="form--control"
                                value="{{ get_country_phone_code_by_iso2($info['mobile_code']) }}{{ $info['mobile_number'] }}">
                            <input name="amount" class="d-none" value="{{ $info['amount'] }}">
                            @if ($info['mobile_code'] === "NG")
                                <input name="service_id" class="d-none" value="{{ $info['provider'] }}">
                                <input name="variation_code" class="d-none" value="{{ $info['variation_code'] }}">
                            @else 
                                <input name="operator_id" class="d-none" value="{{ $charges['operator']['operatorId'] }}">
                            @endif
                            {{-- <input name="geo_location" class="d-none" value="{{ $info['geo_location'] }}"> --}}
                            <input name="iso2" class="d-none" value="{{ $info['mobile_code'] }}">
                            <input name="charges" class="d-none" value="{{ json_encode($charges) }}">
                            <input name="info" class="d-none" value="{{ json_encode($info) }}" >
                            <div class="mobile-icon">
                                <i class="las la-mobile"></i>
                            </div>
                        </div>
                        <div class="planbuy-btn pt-20">
                            <button type="submit" class="btn--base w-100">{{ __('Buy Now') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
