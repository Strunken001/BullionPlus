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
                    <h3 class="title"><i class="fas fa-info-circle text--base mb-20"></i>{{ __('Your Utility Bill Preview') }}</h3>
                    <div class="plan-preview">
                        <div class="plan-name">
                            <span class="badge badge--base">{{ __('Utility Bill') }}</span>
                        </div>
                        <div class="plan-quantity">
                            <span>{{ $charges['name'] }}</span>
                        </div>
                    </div>
                    <div class="plan-preview">
                        <div class="plan-name">
                            <span class="badge badge--base">{{ __('Service Type') }}</span>
                        </div>
                        <div class="plan-quantity">
                            <span>{{ $charges['service_type'] }}</span>
                        </div>
                    </div>
                    <div class="plan-preview">
                        <div class="plan-name">
                            <span class="badge badge--base">{{ __('Price') }}</span>
                        </div>
                        <div class="plan-quantity">
                            <span>{{ $info['amount'] }} {{ $charges['currency_code'] }}</span>
                        </div>
                    </div>
                    <div class="plan-preview">
                        <div class="plan-name">
                            <span class="badge badge--base">{{ __('Exchange Rate') }}</span>
                        </div>
                        <div class="plan-quantity">
                            <span>1 {{ $charges['currency_code'] }} = {{ $charges['rate'] }} {{ $charges['wallet_currency_code'] }}</span>
                        </div>
                    </div>
                    <div class="plan-preview">
                        <div class="plan-name">
                            <span class="badge badge--base">{{ __('Total Payable') }}</span>
                        </div>
                        <div class="plan-quantity">
                            <span>{{ $info['amount'] * $charges['rate'] }} {{ $charges['wallet_currency_code'] }}</span>
                        </div>
                    </div>
                    <form action="{{ setRoute('user.utility.bill.pay') }}" method="POST">
                        @csrf
                        
                        <input name="charges" class="d-none" value="{{ json_encode($charges) }}">
                        <input name="biller_id" class="d-none" value="{{ $info['biller_id'] }}">
                        <input name="account_number" class="d-none" value="{{ $info['account_number'] }}">
                        <input name="amount" class="d-none" value="{{ $info['amount'] }}">
                        <div class="planbuy-btn pt-20">
                            <button type="submit" class="btn--base w-100">{{ __('Buy Now') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
