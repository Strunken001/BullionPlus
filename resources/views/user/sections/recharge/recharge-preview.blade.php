<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Flexiplan preview
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="flexiplan-preview-section ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9 col-sm-12">
                <div class="get-package-area">
                    <form action="{{ setRoute('user.recharge.submit') }}" method="POST">
                        @csrf
                        <h3 class="title"><i
                                class="fas fa-info-circle text--base mb-20"></i>{{ __('Add Money Preview') }}</h3>
                        <div class="plan-preview">
                            <div class="plan-name">
                                <span class="badge badge--base">{{ __('Amount') }}</span>
                            </div>
                            <div class="plan-quantity">
                                <span>{{ $details['amount'] }} {{ get_default_currency_code() }}</span>
                            </div>
                        </div>
                        <input class="d-none" type="number" name="amount" value="{{ $details['amount'] }}">
                        <input class="d-none" type="text" name="gateway" value="{{ $request->gateway }}">
                        <input class="d-none" type="text" name="gateway_currency" value="{{ $request->gateway_currency }}">
                        <input class="d-none" type="text" name="invoice" value="{{ $details['invoice'] }}">
                        <div class="plan-preview">
                            <div class="plan-name">
                                <span class="badge badge--base">{{ __('Payment Method') }}</span>
                            </div>
                            <div class="plan-quantity">
                                <span>{{ $details['payment_method'] }}</span>
                            </div>
                        </div>
                        <div class="plan-preview">
                            <div class="plan-name">
                                <span class="badge badge--base">{{ __('Charge') }}</span>
                            </div>
                            <div class="plan-quantity">
                                <span>{{ $details['total_charge'] }} {{ $details['currency'] }}</span>
                            </div>
                        </div>
                        <div class="plan-preview">
                            <div class="plan-name">
                                <span class="badge badge--base">{{ __('Total Payable') }}</span>
                            </div>
                            <div class="plan-quantity">
                                <span>{{ $details['total_payable'] }} {{ $details['currency'] }}</span>
                            </div>
                        </div>
                        <div class="planbuy-btn pt-20">
                            <button type="submit" class="btn--base w-100">{{ __('Confirm') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
