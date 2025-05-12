@extends('user.layouts.master')
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __($page_title)])
@endsection
@section('content')
<div class="my-gift-card ptb-80">
    <div class="container">
        <div class="giftcard-list">
            <div class="table-header-title">
                <div class="table-name mb-10">
                    <h3 class="title">{{ __($page_title) }}</h3>
                </div>
                <div class="add-btn text-end mb-10">
                    <a href="{{ setRoute('user.gift.card.list') }}" class="btn--base"><i class="fas la-plus"></i> {{ __('Add Gift Card') }}</a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    <th>{{ __("TRX ID") }}</th>
                                    <th>{{ __("Card Name") }}</th>
                                    <th>{{ __("Card Images") }}</th>
                                    <th>{{ __("Receiver Email") }}</th>
                                    <th>{{ __("Receiver Phone") }}</th>
                                    <th>{{ __("Card Unit Price") }}</th>
                                    <th>{{ __("Card Quantity") }}</th>
                                    <th>{{ __("Card Total Price") }}</th>
                                    <th>{{ __("Exchange Rate") }}</th>
                                    <th>{{ __("Payable Unit Price") }}</th>
                                    <th>{{ __("Total Charge") }}</th>
                                    <th>{{ __("Payable Amount") }}</th>
                                    <th>{{ __("Status") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($giftCards ?? [] as $item)
                                <tr>
                                    <td>{{ $item->trx_id}}</td>
                                    <td>{{ $item->card_name}}</td>
                                    <td><img style="max-width: 50px" src="{{ $item->card_image}} " alt=""></td>
                                    <td>{{ $item->recipient_email}}</td>
                                    <td>+{{ $item->recipient_phone}}</td>
                                    <td>{{ get_amount($item->card_amount,$item->card_currency)}}</td>
                                    <td>{{ $item->qty}}</td>
                                    <td>{{ get_amount($item->card_total_amount,$item->card_currency)}}</td>
                                    <td>{{ get_amount(1,$item->card_currency) ." = ". get_amount($item->exchange_rate,$item->user_wallet_currency)}}</td>
                                    <td>{{ get_amount($item->unit_amount,$item->user_wallet_currency)}}</td>
                                    <td>{{ get_amount($item->total_charge,$item->user_wallet_currency)}}</td>
                                    <td>{{ get_amount($item->total_payable,$item->user_wallet_currency)}}</td>
                                    <td><span class="{{ $item->stringStatus->class }}">{{ __($item->stringStatus->value) }} </span></td>
                                </tr>
                                @empty
                                @include('admin.components.alerts.empty2',['colspan' => 13])

                                @endforelse

                            </tbody>
                        </table>
                        <nav>
                            {{ get_paginate($giftCards) }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
@push('script')

@endpush
