<section class="airtime-topup-section ptb-80">
    <div class="container">
        <div class="airtime-topup-area">
            <div class="row justify-content-center mb-30-none">
                <div class="col-lg-7 col-md-12 mb-30">
                    <div class="airtime-topup-form">
                        <div class="area-title">
                            <span class="dash-payment-badge">!</span>
                            <h3 class="title">{{ __('Utility Bill Payment') }}</h3>
                        </div>
                        <form class="card-form" action="{{ setRoute('user.utility.bill.preview') }}"
                            method="POST">
                            @csrf
                            <div class="row mt-40">
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
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Utility Bill')}}<span>*</span></label>
                                    <select id="provider-select" class="select2-auto-tokenize" name="biller_id" required>
                                        <option value="">{{ __('Select Bill') }}</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label>{{ __('Account Number') }}<span>*</span></label>
                                    <input type="text" class="form--control number-input" name="account_number"
                                        placeholder="{{ __('Enter Account Number') }}"
                                        value="{{ old('account_number') }}">
                                    <span class="btn-ring-input"></span>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label>{{ __('Amount') }}<span>*</span></label>
                                    <input type="text" class="form--control number-input" name="amount"
                                        placeholder="{{ __('Enter Amount') }}"
                                        value="{{ old('amount') }}">
                                    <span class="btn-ring-input"></span>
                                </div>

                                {{-- <div class="add_item"> --}}

                                </div>
                                <div class="col-lg-12 form-group">
                                    <div class="note-area">
                                        <code class="d-block text--base">{{ __('Available Balance') }} :
                                            {{ authWalletBalance() }} {{ get_default_currency_code() }}</code>
                                    </div>
                                </div>
                            </div>

                            <input type="submit" class="btn btn--base w-100 mt-20"
                                value="{{ __('Continue') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('script') 
<script>
    $(document).ready(function () {
        const countrySelect = $('select[name="mobile_code"]');
        const providerSelect = $('#provider-select');

        countrySelect.on('change', function () {
            let iso2 = $(this).val();
            providerSelect.html('<option value="">{{ __("Loading...") }}</option>');

            $.ajax({
                url: '{{ route("user.utility.bill.get.billers") }}',
                type: 'GET',
                data: { iso2: iso2 },
                success: function (response) {
                    if (response.status) {
                        let options = '<option value="">{{ __("Select Utility Bill") }}</option>';
                        $.each(response.data.content, function (index, provider) {
                            options += `<option value="${provider.id}" 
                                data-min="${provider.minLocalTransactionAmount}" 
                                data-max="${provider.maxLocalTransactionAmount}">
                                ${provider.name}
                            </option>`;
                        });
                        providerSelect.html(options);
                    } else {
                        providerSelect.html('<option value="">{{ __("No Utility Bill Found") }}</option>');
                    }
                },
                error: function () {
                    providerSelect.html('<option value="">{{ __("Failed to load providers") }}</option>');
                }
            });
        });

        $('.card-form').on('submit', function (e) {
            const selectedOption = $('#provider-select option:selected');
            const min = parseFloat(selectedOption.data('min'));
            const max = parseFloat(selectedOption.data('max'));
            const amount = parseFloat($('input[name="amount"]').val());

            if (isNaN(amount) || amount < min || amount > max) {
                e.preventDefault();
                alert(`Amount must be between ${min} and ${max} NGN`);
            }
        });

        // Trigger initial load
        countrySelect.trigger('change');
    });
</script>
@endpush



