<section class="airtime-topup-section ptb-80">
    <div class="container">
        <div class="airtime-topup-area">
            <div class="row justify-content-center mb-30-none">
                <div class="col-lg-7 col-md-12 mb-30">
                    <div class="airtime-topup-form">
                        <div class="area-title">
                            <span class="dash-payment-badge">!</span>
                            <h3 class="title">{{ __('Data Bundle') }}</h3>
                        </div>
                        <form class="card-form" action="{{ setRoute('user.data.bundle.preview') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="country_code">
                            <input type="hidden" name="phone_code">
                            <input type="hidden" name="exchange_rate">
                            <input type="hidden" name="name">
                            <input type="hidden" name="operator">
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
                                    <label>{{ __('Provider')}}<span>*</span></label>
                                    <select id="provider-select" class="select2-auto-tokenize" name="provider" required>
                                        <option value="">{{ __('Select Provider') }}</option>
                                    </select>
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Data Bundle')}}<span>*</span></label>
                                    <select id="bundle-select" class="select2-auto-tokenize" name="bundle_amount" required>
                                        <option value="">{{ __('Select Bundle') }}</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label>{{ __('Mobile Number') }}<span>*</span></label>
                                    <input type="text" class="form--control number-input" name="mobile_number"
                                        placeholder="{{ __('Enter Mobile Number') }}"
                                        value="{{ old('mobile_number') }}">
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
        const bundleSelect = $('#bundle-select');

        countrySelect.on('change', function () {
            let iso2 = $(this).val();
            providerSelect.html('<option value="">{{ __("Loading...") }}</option>');

            $.ajax({
                url: '{{ route("user.data.bundle.get.operators") }}',
                method: 'GET',
                data: { iso2: iso2 },
                success: function (response) {
                    providerSelect.empty();
                    providerSelect.append('<option value="">{{ __("Select Provider") }}</option>');
                    
                    if (response.data && Array.isArray(response.data.operators) && response.data.operators.length > 0) {
                        response.data.operators.forEach(function (op) {
                            providerSelect.append(`<option value="${op.operatorId}" data-name="${op.name}" data-operator='${JSON.stringify(op)}'>${op.name}</option>`);
                        });
                    } else {
                        providerSelect.append('<option value="">{{ __("No Providers Found") }}</option>');
                    }
                },
                error: function () {
                    providerSelect.html('<option value="">{{ __("Failed to load providers") }}</option>');
                }
            });
        });

        countrySelect.trigger('change');

        // $('#provider-select').on('change', function () {
        //     let selected = $(this).find('option:selected');
        //     let operatorData = selected.data('operator');

        //     console.log(selected.val());

        //     $.ajax({
        //         url: '{{ route("user.data.bundle.get.packages") }}',
        //         method: 'GET',
        //         data: {
        //             operator_id: selected.val(),
        //         },
        //         success: function (response) {
        //             bundleSelect.empty();
        //             bundleSelect.append('<option value="">{{ __("Select Bundle") }}</option>');

        //             if (response.data && Array.isArray(response.data.bundles) && response.data.bundles.length > 0) {
        //                 response.data.bundles.forEach(function (bundle) {
        //                     bundleSelect.append(`<option value="${bundle.amount}">${bundle.description}</option>`);
        //                 });
        //             } else {
        //                 bundleSelect.append('<option value="">{{ __("No Bundles Available") }}</option>');
        //             }
        //         },
        //         error: function () {
        //             bundleSelect.html('<option value="">{{ __("Failed to load bundles") }}</option>');
        //         }
        //     })

        //     $('input[name="operator"]').val(selected.val());
        //     // $('input[name="name"]').val(selected.data('name'));

        //     $('#bundle-select').on('change', function () {
        //         let selectedOption = $(this).find('option:selected');
        //         let bundleDescription = selectedOption.text();

        //         $('input[name="name"]').val(bundleDescription);
        //     });
        // });

        $('#provider-select').on('change', function () {
            const selected = $(this).find('option:selected');
            const operatorData = selected.data('operator');

            bundleSelect.empty();
            bundleSelect.append('<option value="">{{ __("Select Bundle") }}</option>');

            if (operatorData && operatorData.localFixedAmountsDescriptions) {
                Object.entries(operatorData.localFixedAmountsDescriptions).forEach(([amount, description]) => {
                    bundleSelect.append(`<option value="${amount}">${description}</option>`);
                });
            } else {
                bundleSelect.append('<option value="">{{ __("No Bundles Available") }}</option>');
            }

            $('input[name="operator"]').val(operatorData.operatorId);
        });

        $('#bundle-select').on('change', function () {
            const selectedOption = $(this).find('option:selected');
            $('input[name="name"]').val(selectedOption.text());
        });
    });
</script>
@endpush

