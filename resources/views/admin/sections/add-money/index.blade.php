@extends('admin.layouts.master')

@push('css')
@endpush
@section('page-title')
    @include('admin.components.page-title', ['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('admin.dashboard'),
            ],
        ],
        'active' => __('Add Money Logs'),
    ])
@endsection

@section('content')
    <div class="table-area">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ $page_title }}</h5>
                <div class="table-btn-area">
                    @include('admin.components.search-input', [
                        'name' => 'transaction_search',
                    ])
                </div>
            </div>
            <div class="table-responsive">
                <table class="custom-table transaction-search-table">
                    <thead>
                        <tr>
                            <th>{{ __('SL') }}</th>
                            <th>{{ __('TRX ID') }}</th>
                            <th>{{ __('Full Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Username') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Method') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Time') }}</th>
                            <th>{{ __("ACTION")}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions ?? []  as $key => $item)
                            <tr>
                                <td>{{ $transactions->firstItem()+$loop->index}}</td>
                                <td>{{ $item->trx_id }}</td>
                                <td>{{ $item->user->username }}</td>
                                <td>{{ $item->user->email }}</td>
                                <td>{{ $item->user->username }}</td>
                                <td>{{ $item->user->full_mobile ?? '' }}</td>
                                <td>{{ get_amount($item->request_amount, get_default_currency_code()) }}</td>
                                <td><span class="text--info">{{ $item['gateway_currency']->name }}</span></td>
                                <td>
                                    <span
                                        class="{{ $item->stringStatus->class }}">{{ __($item->stringStatus->value) }}</span>
                                </td>
                                <td>{{ $item->created_at->format('d-m-y h:i:s A') }}</td>
                                <td>
                                    @include('admin.components.link.info-default', [
                                        'href' => setRoute('admin.add.money.details', $item->id),
                                        'permission' => 'admin.add.money.details',
                                    ])
                                </td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty', ['colspan' => 11])
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ get_paginate($transactions) }}
        </div>
    </div>
@endsection

@push('script')
    <script>
        itemSearch($("input[name=transaction_search]"), $(".transaction-search-table"),
            "{{ setRoute('admin.add.money.search') }}", 1);
    </script>
@endpush
