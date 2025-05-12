@extends('user.layouts.master')
@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp
@push('css')
@endpush

@section('content')
    @include('user.sections.recharge.recharge')
@endsection

@push('script')
    <script>
        $(document).on("change", ".select-gateway", function() {
            var selectedGateway = $(this);
            var dataKey = selectedGateway.attr("data-key");
            var supportedCurrencies = JSON.parse(selectedGateway.attr("data-supported-currency"));
            var $select = $('<select class="form--control currency-select select-2" name="gateway_currency" required></select>');
            $select.append(`<option value="" selected>Select Currency</option>`);
            $.each(supportedCurrencies, function(index, currency) {
                $select.append(`<option value="${currency['alias']}">${currency['currency_code']}</option>`);
            });
            $('.currency-select').empty().append($select);
        });
    </script>
@endpush
