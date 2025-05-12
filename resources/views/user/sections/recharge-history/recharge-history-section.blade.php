<!-- Purchase History -->
<section class="purchase-history-section ptb-80">
    <div class="container">
        <div class="purchase-history-area">
            <div class="dashboard-list-area">
                <div class="dashboard-header-wrapper">
                    <h3 class="title">{{ __('Your Recharge History') }}</h3>
                </div>
            </div>
            <div class="history-sections">
                <div class="internet-history">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-striped custom-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ __('SL') }}</th>
                                            <th scope="col">{{ __('Recharge amount') }}</th>
                                            <th scope="col">{{ __('Total cost') }}</th>
                                            <th scope="col">{{ __('Paid with') }}</th>
                                            <th scope="col">{{ __('TRX type') }}</th>
                                            <th scope="col">{{ __('Status') }}</th>
                                            <th scope="col">{{ __('Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions ?? [] as $value)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <th>{{ get_amount($value->request_amount, get_default_currency_code()) }}
                                                </th>
                                                <td>{{ get_amount($value->total_payable, $value->payment_currency) }}
                                                </td>
                                                <td>{{ $value->gateway_currency->name }}</td>
                                                <td>{{ $value->gateway_currency->gateway->type }}</td>
                                                <td>
                                                    @if ($value->status === 1)
                                                        <p>{{ __('Complete') }}</p>
                                                    @elseif ($value->status === 2)
                                                        <p>{{ __('Pending') }}</p>
                                                    @elseif ($value->status === 4)
                                                        <p>{{ __('Canceled') }}</p>
                                                    @endif
                                                </td>
                                                <td>{{ $value->created_at }}</td>
                                            </tr>
                                        @empty
                                        <tr>
                                            <th class="text-center" colspan="7">{{__('Nothing to show yet') }}</th>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if (count($transactions ?? []) > 0)
                                    {{ $transactions->withQueryString()->setPath(url()->current())->links('pagination::bootstrap-5') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
