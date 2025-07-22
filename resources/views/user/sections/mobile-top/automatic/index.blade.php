<section class="airtime-topup-section ptb-80">
    <div class="container">
        <div class="airtime-topup-area">
            <div class="airtime-title pb-20">
                <div class="row">
                    <div class="col-xl-8 col-lg-10">
                        <h2 class="title">{{ __(@$page_title) }}</h2>
                        <p>{{ __($section_data->value->language->$defualt->description ?? $section_data->value->language->$default_lng->description) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="row mb-30-none">
                <div class="col-lg-7 col-md-12 mb-30">
                    <div class="airtime-topup-form">
                        <div class="area-title">
                            <span class="dash-payment-badge">!</span>
                            <h3 class="title">{{ __('Mobile Topup') }}</h3>
                        </div>
                        <form class="card-form" action="{{ setRoute('user.mobile.topup.automatic.pay') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="country_code">
                            <input type="hidden" name="phone_code">
                            <input type="hidden" name="exchange_rate">
                            <input type="hidden" name="operator">
                            <input type="hidden" name="operator_id">
                            <div class="row">
                                <div class="col-lg-12 text-center mb-40">
                                    <div class="amount-form-header">
                                        <p class="sub-title"><span class="fees-show">--</span></p>
                                        <h4 class="limit-show rate"></h4>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Country Code') }}<span>*</span></label>
                                    <select class="select2-auto-tokenize" name="mobile_code">
                                        @foreach (get_all_countries(global_const()::USER) ?? [] as $key => $code)
                                            <option value="{{ $code->iso2 }}"
                                                data-mobile-code="{{ remove_speacial_char($code->mobile_code) }}"
                                                {{ $code->name === auth()->user()->address->country ? 'selected' : '' }}>
                                                {{ $code->name . ' (+' . remove_speacial_char($code->mobile_code) . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-6 form-group">
                                    <label>{{ __('Mobile Number') }}<span>*</span></label>
                                    <input type="text" class="form--control number-input" name="mobile_number"
                                        placeholder="{{ __('Enter Mobile Number') }}"
                                        value="{{ old('mobile_number') }}">
                                    <span class="btn-ring-input"></span>
                                </div>

                                <div class="add_item">

                                {{-- <div class="col-xxl-12 col-xl-12 col-lg-12 form-group">
                                    <label>{{ __('Amount') }}<span>*</span></label>
                                    <div class="input-group currency-type">
                                        <input type="text" class="form--control number-input" required placeholder="{{ __('Enter Amount') }}" name="amount" value="{{ old('amount') }}">
                                    </div>
                                </div> --}}

                                </div>
                                <div class="col-lg-12 form-group">
                                    <div class="note-area">
                                        <code class="d-block text--base">{{ __('Available Balance') }} :
                                            {{ authWalletBalance() }} {{ get_default_currency_code() }}</code>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div id="operator-loader" style="display: none;" class="text-center my-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">{{ __('Loading...') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-12 mb-30">
                    <div class="transfer-preview-area">
                        <div class="area-title">
                            <span class="dash-payment-badge">!</span>
                            <h3 class="title">{{ __('Preview') }}</h3>
                        </div>
                        <div class="preview-list-wrapper">
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-sort"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Operator Name') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span class="text--success topup-type">--</span>
                                </div>
                            </div>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-phone-alt"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Mobile Number') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span class="text--warning mobile-number">--</span>
                                </div>
                            </div>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-dollar-sign"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Amount') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span class="text--danger request-amount">--</span>
                                </div>
                            </div>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-money-check-alt"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Charge') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span class="text--info fees">--</span>
                                </div>
                            </div>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="la la-money-bill-alt"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Exchange Rate') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span class="rate-show">--</span>
                                </div>
                            </div>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-exchange-alt"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Conversion Amount') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span class="text--info conversion-amount">--</span>
                                </div>
                            </div>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="la la-money-bill"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Total Charge') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span class="fees">--</span>
                                </div>
                            </div>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="la la-wallet"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Total Payable') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span class="text--base last payable-total">--</span>
                                </div>
                            </div>
                            <div class="topup-btn mt-3">
                                <button type="submit"
                                    class="btn--base w-100 btn-loading mobileTopupBtn d-none">{{ __('Recharge Now') }} <i
                                        class="fas fa-mobile ms-1"></i></button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


@push('script')
    <script>
        var defualCurrency = "{{ get_default_currency_code() }}";
        var defualCurrencyRate = "{{ get_default_currency_rate() }}";
        let debounceTimer;

        $('.mobileTopupBtn').attr('disabled', true);
        $("select[name=mobile_code]").change(function() {
            if (acceptVar().mobileNumber != '') {
                checkOperator();
            }
        });
        $("input[name=mobile_number]").on("keyup", function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                checkOperator();
            }, 300);
        });
        $(document).on("click", ".radio_amount", function() {
            preview();
        });
        $(document).on("focusout", "input[name=amount]", function() {
            var operator = JSON.parse($("input[name=operator]").val());
            var denominationType = operator.denominationType;
            if (denominationType === "RANGE") {
                if (($("input[name=amount]").val() !== "") ) {
                    enterLimit();
                }
            }
            preview();
            if ($("input[name=amount]").val() !== "") {
                $('.mobileTopupBtn').removeClass('d-none');
            }
        });
        $(document).on("keyup", "input[name=amount]", function() {
            preview();
        });

        function acceptVar() {
            var selectedMobileCode = $("select[name=mobile_code] :selected");
            var mobileNumber = $("input[name=mobile_number]").val();
            var currencyCode = defualCurrency;
            var currencyRate = defualCurrencyRate;
            var currencyMinAmount = "{{ getAmount($topupCharge->min_limit) }}";
            var currencyMaxAmount = "{{ getAmount($topupCharge->max_limit) }}";
            var currencyFixedCharge = "{{ getAmount($topupCharge->fixed_charge) }}";
            var currencyPercentCharge = "{{ getAmount($topupCharge->percent_charge) }}";
            return {
                selectedMobileCode: selectedMobileCode,
                mobileNumber: mobileNumber,
                currencyCode: currencyCode,
                currencyRate: currencyRate,
                currencyMinAmount: currencyMinAmount,
                currencyMaxAmount: currencyMaxAmount,
                currencyFixedCharge: currencyFixedCharge,
                currencyPercentCharge: currencyPercentCharge,

            };
        }

        function checkOperator() {
            $('#operator-loader').show();
            var url = '{{ route('user.mobile.topup.automatic.check.operator') }}';
            var mobile_code = acceptVar().selectedMobileCode.data('mobile-code');
            var phone = acceptVar().mobileNumber;
            var iso = acceptVar().selectedMobileCode.val();
            var token = '{{ csrf_token() }}';
            $('.mobileTopupBtn').addClass('d-none');

            var data = {
                _token: token,
                mobile_code: mobile_code,
                phone: phone,
                iso: iso
            };

            $.post(url, data, function(response) {
                $('.btn-ring-input').show();
                if (response.status === true) {
                    var response_data = response.data;

                    var destination_currency_code = response_data.destinationCurrencyCode;
                    var destination_currency_symbol = response_data.destinationCurrencySymbol;
                    var denominationType = response_data.denominationType;
                    var destination_exchange_rate = response_data.fx.rate;
                    $('.add_item').empty();
                    $('.limit-show').empty();

                    var minAmount = 0;
                    var maxAmount = 0;
                    if (denominationType === "RANGE") {
                        var senderCurrencyCode = response_data.senderCurrencyCode;
                        var supportsLocalAmounts = response_data.supportsLocalAmounts;
                        if (supportsLocalAmounts == true && destination_currency_code == senderCurrencyCode &&
                            response_data.localMinAmount == null && response_data.localMaxAmount == null) {
                            minAmount = response_data.minAmount;
                            maxAmount = response_data.maxAmount;
                        } else if (supportsLocalAmounts == true && response_data.localMinAmount != null &&
                            response_data.localMaxAmount != null) {
                            minAmount = response_data.localMinAmount;
                            maxAmount = response_data.localMaxAmount;

                        } else {
                            minAmount = response_data.minAmount;
                            maxAmount = response_data.maxAmount;
                        }

                        // Append the HTML code to the .add_item div for RANGE
                        $('.add_item').html(`
                        <div class="col-xxl-12 col-xl-12 col-lg-12 form-group">
                            <label>{{ __('Amount') }}<span>*</span></label>
                            <div class="input-group currency-type">
                                <input type="text" class="form--control number-input" required placeholder="{{ __('Enter Amount') }}" name="amount" value="{{ old('amount') }}">
                                <select class="currency" name="currency" disabled>
                                    <option value="${destination_currency_code}">${destination_currency_code}</option>
                                </select>
                            </div>
                            <div class="quick-reacharge">
                            <div class="quick-recharge-tag">
                                <label>{{ __('Quick Topup') }} :</label>
                            </div>
                            <div class="quick-reacharge-amount">
                                @forelse ($quick_buttons->buttons->items ?? [] as $bttn)
                                <div class="recharge-amount">
                                    <input type="radio" name="q-recharge" data-recharge-value="{{ $bttn->amount }}" class="hide-input q-recharge"
                                        id="recharge-{{ $loop->iteration }}">
                                    <label for="recharge-{{ $loop->iteration }}" class="recharge--amount">
                                        <p>{{ __($bttn->amount) }} ${destination_currency_code }</p>
                                    </label>
                                </div>
                                @empty

                                @endforelse
                            </div>
                        </div>
                    `);
                        $("select[name=currency]").niceSelect();

                        $('.limit-show').html(`
                        <span class="limit-show">{{ __('Limit') }}: ${minAmount+" "+destination_currency_code+" - "+maxAmount+" "+destination_currency_code}</span>
                    `);
                    } else if (denominationType === "FIXED") {
                        var fixedAmounts = response_data.fixedAmounts;
                        // Multiply each value in fixedAmounts array by destination_exchange_rate
                        var multipliedAmounts = fixedAmounts.map(function(amount) {
                            return (amount * destination_exchange_rate).toFixed(
                                2); // Set precision to two decimal places
                        });
                        // Generate radio input fields for each multiplied amount
                        var radioInputs = '';
                        $.each(multipliedAmounts, function(index, amount) {
                            // Check the first radio button by default
                            var checked = index === 0 ? 'checked' : '';
                            radioInputs += `
                            <div class="gift-card-radio-item">
                                <input type="radio" id="level-${index}" name="amount" value="${amount}" onclick="handleRadioClick(this)" class="radio_amount" ${checked}>
                                <label for="level-${index}">${amount} ${destination_currency_code}</label>
                            </div>
                        `;
                        });
                        // Append the HTML code to the .add_item div for FIXED with radio input fields
                        $('.add_item').html(`
                        <div class="col-xl-12 mb-20">
                            <label>{{ __('Amount') }}<span>*</span></label>
                            <div class="gift-card-radio-wrapper">
                                ${radioInputs}
                            </div>
                        </div>
                    `);

                    }
                    $("input[name=operator]").val(JSON.stringify(response_data));
                    feesCalculation();
                    getFee();
                    getExchangeRate();
                    preview();
                    if (denominationType === "FIXED") {
                        var firstRadio = $('input[type="radio"]:first');
                        firstRadio.prop('checked', true);
                        handleRadioClick(firstRadio[0]);
                    }
                    $('.mobileTopupBtn').attr('disabled', false);
                    setTimeout(function() {
                        $('.btn-ring-input').hide();
                        $('#operator-loader').hide();
                    }, 1000);
                } else if (response.status === false && response.from === "error") {
                    $('.add_item, .limit-show').empty();
                    $('.fees-show, .rate-show, .topup-type, .mobile-number, .request-amount, .conversion-amount, .fees, .payable-total')
                        .html('--');
                    $('input[name=phone_code], input[name=country_code],input[name=operator],input[name=operator_id],input[name=exchange_rate]')
                        .val('');
                    $('.mobileTopupBtn').attr('disabled', true);
                    setTimeout(function() {
                        $('.btn-ring-input').hide();
                        $('#operator-loader').hide();
                        throwMessage('error', [response.message]);
                    }, 1000);
                    return false;
                }

                var $rechargeInput = $('[name=amount]');
                $('[name=q-recharge]').on('click', function() {
                    $rechargeInput.val($(this).data('recharge-value'));
                    preview();
                });
            });
        }

        function feesCalculation(exchangeRate) {
            var currencyCode = acceptVar().currencyCode;
            var currencyRate = acceptVar().currencyRate;
            var sender_amount = parseFloat(get_amount());
            sender_amount == "" ? (sender_amount = 0) : (sender_amount = sender_amount);

            var fixed_charge = acceptVar().currencyFixedCharge;
            var percent_charge = acceptVar().currencyPercentCharge;
            if ($.isNumeric(percent_charge) && $.isNumeric(fixed_charge) && $.isNumeric(sender_amount)) {
                // Process Calculation
                var fixed_charge_calc = parseFloat(currencyRate * fixed_charge);
                var percent_charge_calc = (parseFloat(sender_amount) / 100) * parseFloat(percent_charge);
                var total_charge = parseFloat(fixed_charge_calc) + (parseFloat(percent_charge_calc) * exchangeRate);
                total_charge = parseFloat(total_charge).toFixed(4);
                // return total_charge;
                return {
                    total: total_charge,
                    fixed: fixed_charge_calc,
                    percent: percent_charge,
                };
            } else {
                // return "--";
                return false;
            }
        }

        function getFee() {
            var currencyCode = acceptVar().currencyCode;
            var percent = acceptVar().currencyPercentCharge;
            var charges = feesCalculation();
            if (charges == false) {
                return false;
            }
            $(".fees-show").html("{{ __('Topup Fee') }}: " + parseFloat(charges.fixed).toFixed(2) + " " + currencyCode +
                " + " + parseFloat(charges.percent).toFixed(2) + "%  ");

        }

        function getExchangeRate() {
            var walletCurrencyCode = acceptVar().currencyCode;
            var walletCurrencyRate = acceptVar().currencyRate;
            var operator = JSON.parse($("input[name=operator]").val());
            var destination_currency_code = operator.destinationCurrencyCode;
            $.ajax({
                type: 'get',
                url: "{{ route('global.receiver.wallet.currency') }}",
                data: {
                    code: destination_currency_code
                },
                success: function(data) {
                    var receiverCurrencyCode = data.currency_code;
                    var receiverCurrencyRate = data.rate;
                    var exchangeRate = (walletCurrencyRate / receiverCurrencyRate);
                    $("input[name=exchange_rate]").val(exchangeRate);
                    $('.rate-show').html("1 " + receiverCurrencyCode + " = " + parseFloat(exchangeRate).toFixed(4) + " " + walletCurrencyCode);
                    preview(exchangeRate);
                }
            });

        }

        function handleRadioClick(radio) {
            if (radio.checked) {
                amount = parseFloat(radio.value);
                $('.mobileTopupBtn').attr('disabled', false);

            }
        }

        function preview(exchangeRate) {
            var sender_currency = acceptVar().currencyCode;
            var operator = JSON.parse($("input[name=operator]").val());
            var destination_currency_code = operator.destinationCurrencyCode;
            var destination_fixed = operator.fees.local;
            var destination_percent = operator.fees.localPercentage;
            var exchangeRate = parseFloat($("input[name=exchange_rate]").val());
            var senderAmount = parseFloat(get_amount());
            senderAmount == "" ? senderAmount = 0 : senderAmount = senderAmount;

            var conversion_amount = parseFloat(senderAmount) * parseFloat(exchangeRate);
            var phone_code = acceptVar().selectedMobileCode.data('mobile-code');
            var phone = "+" + phone_code + acceptVar().mobileNumber;
            // Fees
            var charges = feesCalculation(exchangeRate);
            var total_charge = 0;
            if (senderAmount == 0) {
                total_charge = 0;
            } else {
                total_charge = parseFloat(charges.total);
            }

            var payable = conversion_amount + total_charge;

            $('.topup-type').text(operator.name);
            $('.mobile-number').text(phone);
            $('.request-amount').text(parseFloat(senderAmount).toFixed(4) + " " + destination_currency_code);
            $('.conversion-amount').text(parseFloat(conversion_amount).toFixed(4) + " " + sender_currency);
            $('.fees').text(parseFloat(total_charge).toFixed(4) + " " + sender_currency);
            $('.payable-total').text(parseFloat(payable).toFixed(4) + " " + sender_currency);
            //hidden filed fullups
            $('input[name=phone_code]').val(phone_code);
            $('input[name=country_code]').val(acceptVar().selectedMobileCode.val());
            $('input[name=operator_id]').val(operator.operatorId);
        }
        var amount = 0;

        function get_amount() {
            var operator = JSON.parse($("input[name=operator]").val());
            var denominationType = operator.denominationType;
            if (denominationType === "RANGE") {
                amount = amount = parseFloat($("input[name=amount]").val());
                if (!($.isNumeric(amount))) {
                    amount = 0;
                } else {
                    amount = amount;
                }
            } else {
                amount = amount;
            }
            return amount;
        }

        function enterLimit() {
            var operator = JSON.parse($("input[name=operator]").val());
            var minAmount = operator.minAmount
            var maxAmount = operator.maxAmount
            var min_limit = parseFloat(minAmount);
            var max_limit = parseFloat(maxAmount);
            var senderAmount = parseFloat(get_amount());

            senderAmount == "" ? senderAmount = 0 : senderAmount = senderAmount;

            if (senderAmount < min_limit) {
                throwMessage('error', ['{{ __('Please follow the mimimum limit') }}']);
                $('.mobileTopupBtn').attr('disabled', true)
            } else if (senderAmount > max_limit) {
                throwMessage('error', ['{{ __('Please follow the maximum limit') }}']);
                $('.mobileTopupBtn').attr('disabled', true)
            } else {
                $('.mobileTopupBtn').attr('disabled', false)
            }

        }
    </script>
    <script>
        const mobileNumber = $("input[name=mobile_number]").val();
        if (mobileNumber.trim() !== '') {
            checkOperator();
        }
        $(function() {
            var $rechargeInput = $('[name=amount]');
            $('[name=q-recharge]').on('click', function() {
                $rechargeInput.val($(this).data('recharge-value'));
            });
        });
    </script>
@endpush
