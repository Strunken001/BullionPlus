@extends('admin.layouts.master')

@push('css')
@endpush

@section('page-title')
    @include('admin.components.page-title', ['title' => __("Bundle Details")])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('admin.dashboard'),
            ],
        ],
        'active' => __('Bundle Details'),
    ])
@endsection

@section('content')

<div class="custom-card">
    <div class="card-header">
        <h6 class="title">{{ __($page_title) }}</h6>
    </div>
    <div class="card-body">
        <form class="card-form">
            <div class="row align-items-center mb-10-none">
                <div class="col-xl-4 col-lg-4 form-group">
                    <ul class="user-profile-list-two">
                        <li class="one">{{ __("Date") }}: <span>{{ @$data->created_at->format('d-m-y h:i:s A') }}</span></li>
                        <li class="two">{{ __("Fullname") }}: <span>
                            @if($data->user_id != null)
                            <a href="{{ setRoute('admin.users.details',$data->user->username) }}">{{ $data->user->fullname }} ({{ __("USER") }})</a>
                            @endif
                            </span>
                        </li>
                        <li class="three">{{ __("TopUp Type") }}: <span class="fw-bold">{{ @$data->details->topup_type_name }}</span></li>
                        <li class="four">{{ __("Mobile Number") }}: <span class="fw-bold">{{ @$data->details->mobile_number }}</span></li>
                        <li class="five">{{ __("Topup Amount") }}: <span class="fw-bold">{{ get_amount($data->request_amount,$data->request_currency) }}</span></li>

                    </ul>
                </div>
                <div class="col-xl-4 col-lg-4 form-group">
                    <div class="user-profile-thumb">
                        <img src="{{ get_image(@$default_currency->flag,'currency-flag') }}" alt="payment">
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 form-group">
                    <ul class="user-profile-list two">
                        <li class="one">{{ __("Exchange Rate") }}: <span>{{$data->exchange_rate }}</span></li>
                        <li class="two">{{ __("Total Charge") }}: <span>{{ get_amount($data->total_charge,$data->payment_currency ?? '') }}</span></li>
                        <li class="three">{{ __("Payable Amount") }}: <span>{{ get_amount($data->total_payable,$data->payment_currency) }}</span></li>
                        <li class="three">{{ __("Remaining Balance") }}: <span>{{ get_amount($data->available_balance,$data->payment_currency) }}</span></li>
                        <li class="four">{{__("Status") }}:  <span class="{{ @$data->stringStatus->class }}">{{ __(@$data->stringStatus->value) }}</span></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection


@push('script')
<script>
    $(document).ready(function(){
        @if($errors->any())
        var modal = $('#rejectModal');
        modal.modal('show');
        @endif
    });
</script>
<script>
     (function ($) {
        "use strict";
        $('.approvedBtn').on('click', function () {
            var modal = $('#approvedModal');
            modal.modal('show');
        });
        $('.rejectBtn').on('click', function () {
            var modal = $('#rejectModal');
            modal.modal('show');
        });
    })(jQuery);





</script>
@endpush
