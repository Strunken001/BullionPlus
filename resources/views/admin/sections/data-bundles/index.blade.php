@extends('admin.layouts.master')

@push('css')

@endpush

@section('page-title')
    @include('admin.components.page-title',['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("admin.dashboard"),
        ]
    ], 'active' => __("Data Bundle Logs")])
@endsection

@section('content')
<div class="table-area">
    <div class="table-wrapper">
        <div class="table-header">
            <h5 class="title">{{ $page_title }}</h5>
            @if(count($transactions) > 0)
            @endif
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>{{ __("TRX ID") }}</th>
                        <th>{{ __("Fullname") }}</th>
                        <th>{{ __("TopUp Type") }}</th>
                        <th>{{ __("Mobile Number") }}</th>
                        <th>{{ __("TOPUP AMOUNT") }}</th>
                        <th>{{ __(("Status")) }}</th>
                        <th>{{ __("Time") }}</th>
                        <th>{{__("Action")}}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions ?? []  as $key => $item)

                        <tr>
                            <td>{{ $item->trx_id }}</td>

                            <td>
                                @if($item->user_id != null)
                                    <a href="{{ setRoute('admin.users.details',$item->user->username) }}">{{ $item->user->username }}</a>
                                @endif
                            </td>

                            <td ><span class="fw-bold">{{ @$item->details->topup_type_name }}</span></td>
                            <td ><span class="fw-bold">{{ @$item->details->mobile_number }}</span></td>
                            <td>{{ get_amount($item->request_amount,$item->request_currency) }}</td>
                            <td>
                                <span class="{{ $item->stringStatus->class }}">{{ __($item->stringStatus->value) }}</span>
                            </td>
                            <td>{{ $item->created_at->format('d-m-y h:i:s A') }}</td>
                            <td>
                                @include('admin.components.link.info-default',[
                                    'href'          => setRoute('admin.data.bundle.details', $item->id),
                                    'permission'    => "admin.data.bundle.details",
                                ])

                            </td>
                        </tr>
                    @empty
                         @include('admin.components.alerts.empty',['colspan' => 10])
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ get_paginate($transactions) }}
    </div>
</div>
@endsection

@push('script')

@endpush
