<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Recharge preview
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="flexiplan-preview-section ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9 col-sm-12">
                <form action="{{ setRoute('user.recharge.recharge.preview') }}" method="POST">
                    @csrf
                    <div class="get-package-area">
                        <h3 class="title">
                            <i class="fas fa-info-circle text--base mb-20"></i> {{ __('Add Money Preview') }}
                        </h3>
                        <div class="plan-preview">
                            <div class="plan-name">
                                <span class="badge badge--base">{{ __('Add Money Amount') }}</span>
                            </div>
                            <div class="plan-quantity">
                                <span><b>{{ $amount }} {{ get_default_currency_code() }}</b></span>
                            </div>
                            <input class="d-none" type="number" name="amount" value="{{ $amount }}">
                        </div>
                        <div class="payment-page-section">
                            <div class="payment-page-area">
                                <div class="payment-type pt-20">
                                    <div class="select-payment-area">
                                        <label class="title">{{ __('Select Payment Method') }}</label>
                                        <div class="radio-wrapper pt-2" id="pg-view">
                                            @forelse ($payment_gateways ?? [] as $key => $gateway)
                                                <div class="radio-item">
                                                    <input type="radio" id="level-{{ $key }}"
                                                        class="hide-input select-gateway" name="gateway" data-key='{{ $key }}' data-supported-currency='{{ $gateway->currencies }}'>
                                                    <label for="level-{{ $key }}">
                                                        <img src="{{ get_image($gateway->image, 'payment-gateways') }}"
                                                            alt="icon">
                                                        {{ __($gateway->name) }}
                                                    </label>
                                                </div>
                                            @empty
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="select-payment-area currency-select-area">
                                        <label class="title">{{ __('Select Payment Currency') }}</label>
                                        <div class="currency-select">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="custom-check-group pt-20">
                            <input type="checkbox" id="level-111" name='invoice'>
                            <label for="level-111">{{ __('Get invoice in my mobile') }}</label>
                        </div>
                        <div class="planbuy-btn pt-20">
                            <button type="submit" class="btn--base w-100">{{ __('Add Money') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
