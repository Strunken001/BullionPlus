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
        'active' => __($page_title),
    ])
@endsection

@section('content')
    <div class="table-area">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ $page_title }}</h5>
                @if (count($profits) > 0)
                    <div class="table-btn-area">
                        <h5 class="title">{{ __('TOTAL PROFITS') }}: {{ getAmount(totalAdminProfits(), 3) }}
                            {{ get_default_currency_code() }}</h5>
                    </div>
                @endif
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>{{ __('TRX ID') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('User Type') }}</th>
                            <th>{{ __('TRANSACTION TYPE') }}</th>
                            <th>{{ __('PROFIT AMOUNT') }}</th>
                            <th>{{ __('Time') }}</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($profits ?? []  as $key => $item)
                            <tr>
                                <td>{{ @$item->transactions->trx_id }}</td>
                                <td>
                                    @if (@$item->transactions->user_id != null)
                                        <a
                                            href="{{ setRoute('admin.users.details', @$item->transactions->user->username) }}">{{ @$item->transactions->user->fullname }}</a>
                                    @endif
                                </td>
                                <td>
                                    {{ __('USER') }}
                                </td>
                                <td>{{ @$item->transactions->type }}</td>
                                <td>{{ number_format(@$item->total_charge, 2) }} {{ get_default_currency_code() }}</td>
                                <td>{{ $item->created_at->format('d-m-y h:i:s A') }}</td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty', ['colspan' => 7])
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ get_paginate($profits) }}
        </div>
    </div>
@endsection

@push('script')
@endpush
