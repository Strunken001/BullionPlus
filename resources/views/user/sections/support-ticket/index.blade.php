@section('content')
    <section class="support-tickets-section ptb-60">
        <div class="container">
            <div class="support-tickets">
                <div class="table-area">
                    <div class="table-wrapper">
                        <div class="dashboard-header-wrapper">
                            <h4 class="title">{{ __('Support Tickets') }}</h4>
                            <div class="dashboard-btn-wrapper">
                                <div class="dashboard-btn">
                                    <a href="{{ route('user.support.ticket.create') }}" class="btn--base"><i
                                            class="las la-plus me-1"></i> {{ __('Add New') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Ticket ID') }}</th>
                                        <th>{{ __('Subject') }}</th>
                                        <th>{{ __('Message') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Last Reply') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($support_tickets as $item)
                                        <tr>
                                            <td>#{{ $item->token }}</td>
                                            <td>{{ $item->subject }}</td>
                                            <td>{{ Str::words($item->desc, 10, '...') }}</td>
                                            <td>
                                                <span class="{{ $item->stringStatus->class }}">
                                                    {{ $item->stringStatus->value }}
                                                </span>
                                            </td>
                                            <td>
                                                @if (getReply($item->id) != null)
                                                    {{ getReply($item->id)->created_at->diffForHumans() }}
                                                @else
                                                    {{ __('Not replyed yet ') }}
                                                @endif
                                            </td>
                                            <td>{{ $item->created_at->format('Y-m-d H:i A') }}</td>
                                            <td>
                                                <a href="{{ route('user.support.ticket.conversation', encrypt($item->id)) }}"
                                                    class="btn btn--base"><i class="las la-comment"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center"><span class="text-warning">{{ __('No Data Found!') }}</span></td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ get_paginate($support_tickets) }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
