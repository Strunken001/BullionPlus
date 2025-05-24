<section class="myoffer-section ptb-80">
    <div class="container">
        <div class="myoffer-area">
            <div class="section-tag">
                <span>
                    <img src="{{ get_fav($basic_settings) }}" data-white_img="{{ get_fav($basic_settings, 'white') }}"
                        data-dark_img="{{ get_fav($basic_settings, 'dark') }}" alt="logo">
                    {{ __('Select a service below') }}
                </span>
            </div>
            {{-- <div class="offer-page-area">
                <div class="offer-page-area-header">
                    <div class="row mb-20-none d-flex justify-content-between">
                        <div class="col-lg-6 col-md-6 col-sm-6 mb-20 country-option">
                            <h4 class="title">{{ __('Select Country') }}*</h4>
                            <select class="form--control country-picker select2-basic trx-type-select" name="country">
                                <option value="" selected disabled>{{ __('Choose One') }}</option>
                                @forelse (get_all_countries_array() ?? [] as $value)
                                    <option value="{{ $value['iso2'] }}">{{ $value['name'] }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 mb-20 operator-select">
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6 mb-20 geo-location-select">
                        </div>
                    </div>
                </div>
                <div class="offer-page-details">
                    <ul class="bundel-list geo-amount mb-20-none">
                        <li class="offer-item mb-20">
                            <div class="bundel-offer text-center inst-text">
                                <p>{{ __('Please select a country') }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div> --}}

            <section class="service-section">
                <div class="container">
                    <div class="service-item-group">
                        <div class="row mb-20-none">
                            @forelse ($services->value->items ?? [] as $value)
                            <div class="col-lg-4 col-md-6 mb-20">
                                <div class="service-item-area">
                                    <a href="{{ setRoute(@$value->link) }}">
                                    <div class="service-icon">
                                        <div class="plan-item">
                                            <img src="{{ get_image($value->image, "site-section") }}" alt="icon">
                                        </div>
                                        <div class="service-details">
                                            <h3 class="title">{{ __(@$value->language->$defualt->title ?? @$value->language->$default_lng->title) }}</h3>
                                            <p>{{ __(@$value->language->$defualt->description ?? @$value->language->$default_lng->description) }}</p>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            </div>
                            @empty

                            @endforelse
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>

@push('script')
    <script>
        let getOperatorsURL = "{{ setRoute('user.data.bundle.get.operators') }}";
        let operatorId = null;
        let storeOperators      = null;
        let operator;
        let operatorCacheKey    = null;
        let operatorCountry = null

        $(document).ready(function() {

        });

        $(document).on("change", "select[name=country]", function() {
            operatorCountry = $(this).val();
            $(".operator-select").html("<p class='spinner-border'></p>");
            $(".geo-location-select").html("");

            let operatorChoose = `<ul class="bundel-list geo-amount mb-20-none">
                                    <li class="offer-item mb-20">
                                        <div class="bundel-offer text-center inst-text">
                                            <p>{{ __('Please select an operator') }}</p>
                                        </div>
                                    </li>
                                </ul>`


            $(".offer-page-details").html(operatorChoose);
            operatorId= null;
            selectedLocation = null;
            getOperators(operatorCountry);
        })



        $(document).on("change", "select[name=operator]", function() {
            let operatorId = $(this).val();

            operator = Object.values(storeOperators).find(item => item.id == operatorId);

            if (!operator || operator == undefined) {
                throwMessage("error", ["{{ __('Invalid Operator Selected!') }}"]);
                return false
            }

            if(operator.supportsGeographicalRechargePlans){
                $('.inst-text').html(`<p>{{ __('Please Select A Geo Location') }}</p>`);
                $('.country-option').removeClass('col-lg-6 col-md-6').addClass('col-lg-4 col-md-4');
                $('.operator-select').removeClass('col-lg-6 col-md-6').addClass('col-lg-4 col-md-4');
                let geoChoose = `<ul class="bundel-list geo-amount mb-20-none">
                                    <li class="offer-item mb-20">
                                        <div class="bundel-offer text-center inst-text">
                                            <p>{{ __('Please Select A Geo Location') }}</p>
                                        </div>
                                    </li>
                                </ul>`


                $(".offer-page-details").html(geoChoose);
            }

            // update selected biller
            selectedOperator = operator;

            genrateOperatorInputField(operator);
        })

        $(document).on("change", "select[name=geo_location]", function() {
            let operator = selectedOperator;
            let selectedLocation = $(this).val();

            let geoRechargePlans     = operator.geographicalRechargePlans ?? [];

            let locationData = Object.values(geoRechargePlans).find(item => item.locationCode == selectedLocation);

            let geoLocation = null;

            if(locationData == undefined || !locationData) {
                throwMessage("error", ['Invalid Location Selected! Reload this page and try again']);
            }else {
                updateGeoRechargePlanField(operator, locationData);
            }
        });



        function getOperators(operatorCountry) {
            // make select empty
            updateOperatorsField(null);
            selectedOperator = null;
            operatorCacheKey = null;

            $(".operator-fields .children").slideUp(300); // remove operator inputs also
            setTimeout(() => {
                $(".operator-fields .children").html("");
            }, 300);

            $(".operator-select").html("<p class='spinner-border'></p>");

            // send request to server
            let formData = { iso2: `${operatorCountry}`};

            fetchData(formData, getOperatorsURL).then((response) => {

                let data = response.data;
                let operators = data.operators;
                let cacheKey = data.cache_key;

                operatorCacheKey = cacheKey;
                updateOperatorsField(operators);
                storeOperators = data.operators;

                operator = Object.values(storeOperators).find(item => item.id == operatorId);
                genrateOperatorInputField(operator);

            }).catch((errors) => {
                throwMessage('error', errors);
            });
        }

        // update operator select box
        function updateOperatorsField(operators) {
            storeOperators = operators;
            let indexValue = null;

            $.each(operators, function(index, item) {
                    indexValue = index;
            });

            if (operators || indexValue) {
                let options = null;

                if(indexValue === null){
                    options += `<option value="" selected disabled>{{ __('No operator found') }}</option>`;
                }
                else{
                    options += `<option value="" selected disabled>{{ __('Choose One') }}</option>`;
                    $.each(operators, function(index, item) {
                        options += `<option value="${item.id}">${item.name}</option>`;
                    });
                }

                let operatorSelectMarkup = `<h4 class="title">{{ __('Select Operator') }} *</h4>
                                            <select name="operator" class="form--control select2">
                                                ${options}
                                            </select>`;

                $(".operator-select").html(operatorSelectMarkup).slideDown(300).find("select").select2();
            } else {
                $(".operator-select").html(`<h4 class="title">{{ __('Select Operator') }} *</h4>`);
            }
        }

        // generate input fields for operators
        function genrateOperatorInputField(operator = null) {

            if (!operator) {
                operator = selectedOperator;
            }

            // remove current values
            $(".operator-fields .children").html("");
            fixedAmountInputFields(operator);

        }


        // fixed input input fields
        function fixedAmountInputFields(operator) {

            // check geographical location
            let isGeoLocationSupport = operator.supportsGeographicalRechargePlans;

            if (isGeoLocationSupport) {
                updateGeoLocationField(operator);
            } else {
                // show local fixed amount
                updateAmountSelectField(operator);
            }

        }

        // update geo location select field
        function updateGeoLocationField(operator){
            let geoRechargePlans = operator.geographicalRechargePlans ?? [];

            let options = `<option value="" selected disabled>{{ __("Choose One") }}</option>`;
            $.each(geoRechargePlans, function(index, item) {
                options += `<option value="${item.locationCode}">${item.locationName}</option>`;
            });

            let htmlMarkup = `
                <label><h4 class="title">{{ __('Select Geo Location') }}*</h4></label>

                <select name="geo_location" class="form--control select2">
                    ${options}
                </select>
            `;

            $(".geo-location-select").html(htmlMarkup).slideDown(300).find("select").select2();
        }


        // update geo recharge plan
        function updateGeoRechargePlanField(operator, location){
            let localAmounts            = location.localAmounts ?? [];
            let localAmountDesc         = location.localFixedAmountsDescriptions ?? [];
            let localAmountPlanNames    = location.localFixedAmountsPlanNames ?? [];

            let merchantAmounts             = location.fixedAmounts ?? [];
            let merchantAmountDesc          = location.fixedAmountsDescriptions ?? [];
            let merchantAmountPlanNames     = location.fixedAmountsPlanNames ?? [];

            let localCurrency = operator.destinationCurrencyCode;

            let localExchangeRate = operator.fx.rate;

            let options = ``;
            if(localAmounts.length > 0) {

                $.each(localAmounts, function(index, item) {
                    let amount = item + " " + localCurrency;

                    let planDetailsKey = Object.keys(localAmountDesc).find(amount => amount == item);

                    let planDetails = localAmountDesc[planDetailsKey] ?? "";
                    let planName    = localAmountPlanNames[planDetailsKey] ?? "";

                    options += `<li class="offer-item mb-20">
                        <div class="bundel-offer"><p>${amount}${planDetails != "" ? " - " + planDetails : ""}</p></div>
                            <form action="{{ setRoute('user.data.bundle.preview') }}" method="POST">
                                @csrf
                                <input class="d-none" name="operator" value="${operator.operatorId}"/>
                                <input class="d-none" name="amount" value="${item}"/>
                                <input class="d-none" name="cache_key" value="${operatorCacheKey}"/>
                                <input class="d-none" name="name" value="${planDetails}"/>
                                <input class="d-none" name="geo_location" value="${location.locationCode}"/>
                                <input class="d-none" name="iso2" value="${operatorCountry}"/>

                                <div class="bundel-buy"><button type="submit" class="btn--base btn">{{ __('Buy Now') }}</button></div>
                            </form>
                        </li>`
                });

            }else {
                // convert merchant amount to location amount
                $.each(merchantAmounts, function(index, item) {
                    let exchangeAmount = parseFloat(localExchangeRate) * parseFloat(item);
                    exchangeAmount = exchangeAmount.toFixed(2) + " " + localCurrency;

                    let planDetailsKey = Object.keys(merchantAmountDesc).find(amount => amount == item);
                    let planDetails = merchantAmountDesc[planDetailsKey] ?? "";

                    let planName    = merchantAmountPlanNames[planDetailsKey] ?? "";

                    options += `<li class="offer-item mb-20">
                        <div class="bundel-offer"><p>${exchangeAmount}${planDetails != "" ? " - " + planDetails : ""}</p></div>
                            <form action="{{ setRoute('user.data.bundle.preview') }}" method="POST">
                                @csrf
                                <input class="d-none" name="operator" value="${operator.operatorId}"/>
                                <input class="d-none" name="amount" value="${item}"/>
                                <input class="d-none" name="cache_key" value="${operatorCacheKey}"/>
                                <input class="d-none" name="name" value="${planDetails}"/>
                                <input class="d-none" name="geo_location" value="${location.locationCode}"/>
                                <input class="d-none" name="iso2" value="${operatorCountry}"/>
                                <div class="bundel-buy"><button type="submit" class="btn--base btn">{{ __('Buy Now') }}</button></div>
                            </form>
                        </li>`
                });
            }

            let htmlMarkup = `
                <ul class=" bundel-list mb-20-none" style="max-height: 600px !important; overflow-y: auto;">
                    ${options}
                </ul>
            `;

            $(".offer-page-details").html(htmlMarkup);


        }


        // update amount select field for FIXED amount operators
        function updateAmountSelectField(operator) {
            let localAmounts        = operator.localFixedAmounts ?? [];
            let localAmountDesc     = operator.localFixedAmountsDescriptions ?? [];

            let merchantAmounts     = operator.fixedAmounts ?? [];
            let merchantAmountDesc  = operator.fixedAmountsDescriptions ?? [];

            let exchangeRate        = operator.fx.rate;
            let localCurrency       = operator.destinationCurrencyCode;

            let options = ``;
            if(localAmounts.length > 0) {
                $.each(localAmounts, function(index, item) {
                    let amount = item + " " + localCurrency;

                    let planDetailsKey = Object.keys(localAmountDesc).find(amount => amount == item);
                    let planDetails = localAmountDesc[planDetailsKey] ?? "";

                    options += `<li class="offer-item mb-20">
                        <div class="bundel-offer"><p>${amount}${planDetails != "" ? " - " + planDetails : ""}</p></div>
                            <form action="{{ setRoute('user.data.bundle.preview') }}" method="POST">
                                @csrf
                                <input class="d-none" name="operator" value="${operator.operatorId}"/>
                                <input class="d-none" name="amount" value="${item}"/>
                                <input class="d-none" name="cache_key" value="${operatorCacheKey}"/>
                                <input class="d-none" name="name" value="${planDetails}"/>
                                <input class="d-none" name="geo_location" value="${null}"/>
                                <input class="d-none" name="iso2" value="${operatorCountry}"/>
                                <div class="bundel-buy"><button type="submit" class="btn--base btn">{{ __('Buy Now') }}</button></div>
                            </form>
                        </li>`
                });
            }else {
                $.each(merchantAmounts, function(index, item) {
                    let exchangeAmount = parseFloat(item) * parseFloat(exchangeRate);
                        exchangeAmount = exchangeAmount.toFixed(2) + " " + localCurrency;

                    let planDetailsKey = Object.keys(merchantAmountDesc).find(amount => amount == item);
                    let planDetails = merchantAmountDesc[planDetailsKey] ?? "";

                    options += `<li class="offer-item mb-20">
                        <div class="bundel-offer"><p>${exchangeAmount}${planDetails != "" ? " - " + planDetails : ""}</p></div>
                            <form action="{{ setRoute('user.data.bundle.preview') }}" method="POST">
                                @csrf
                                <input class="d-none" name="operator" value="${operator.operatorId}"/>
                                <input class="d-none" name="amount" value="${item}"/>
                                <input class="d-none" name="cache_key" value="${operatorCacheKey}"/>
                                <input class="d-none" name="name" value="${planDetails}"/>
                                <input class="d-none" name="geo_location" value="${null}"/>
                                <input class="d-none" name="iso2" value="${operatorCountry}"/>
                                <div class="bundel-buy"><button type="submit" class="btn--base btn">{{ __('Buy Now') }}</button></div>
                            </form>
                        </li>`
                });
            }

            let htmlMarkup = `
                <ul class=" bundel-list mb-20-none" style="max-height: 600px !important; overflow-y: auto;">
                    ${options}
                </ul>
            `;

            $(".offer-page-details").html(htmlMarkup);
        }
    </script>
@endpush
