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
    ], 'active' => __("Support Ticket")])
@endsection

@section('content')
    @include('admin.components.support-ticket.counter-card',['support_tickets' => $all_support_tickets])
    <div class="table-area mt-15">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ __("All Ticket") }}</h5>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>{{ __("Ticket ID") }}</th>
                            <th>{{ __("User") }} ({{ __("Username") }}) </th>
                            <th>{{ __("Subject") }}</th>
                            <th>{{ __("Name") }}</th>
                            <th>{{ __("Message") }}</th>
                            <th>{{ __("Status") }}</th>
                            <th>{{ __("Last Reply") }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($support_tickets as $item)
                            <tr>
                                <td>#{{ $item->token }}</td>
                                <td>
                                    {{ $item->user->username }}
                                </td>
                                <td>
                                    @if ($item->status == support_ticket_const()::DEFAULT)
                                        <span class="text--warning">{{ $item->subject }}</span>
                                    @elseif ($item->status == support_ticket_const()::SOLVED)
                                        <span class="text--success">{{ $item->subject }}</span>
                                    @elseif ($item->status == support_ticket_const()::ACTIVE)
                                        <span class="text--primary">{{ $item->subject }}</span>
                                    @elseif ($item->status == support_ticket_const()::PENDING)
                                        <span class="text--warning">{{ $item->subject }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>{{ Str::words($item->desc, 10, '...') }}</td>
                                <td>
                                    <span class="{{ $item->stringStatus->class }}">{{ __($item->stringStatus->value) }}</span>
                                </td>
                                <td>
                                    @if (count($item->conversations) > 0)
                                        {{ $item->conversations->last()->created_at->format("Y-m-d H:i A") ?? "" }}</td>
                                    @else
                                    {{ __('Not replied yet') }}
                                    @endif
                                <td>
                                    <a href="{{ setRoute('admin.support.ticket.conversation',encrypt($item->id)) }}" class="btn btn--base"><i class="las la-comment"></i></a>
                                </td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty',['colspan' => 9])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(function() {
            $('#chart6').easyPieChart({
                size: 80,
                barColor: '#f05050',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#f050505a',
                lineCap: 'circle',
                animate: 3000
            });
        });
    $(function() {
            $('#chart7').easyPieChart({
                size: 80,
                barColor: '#10c469',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#10c4695a',
                lineCap: 'circle',
                animate: 3000
            });
        });
        $(function() {
            $('#chart8').easyPieChart({
                size: 80,
                barColor: '#ffbd4a',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#ffbd4a5a',
                lineCap: 'circle',
                animate: 3000
            });
        });
        $(function() {
            $('#chart9').easyPieChart({
                size: 80,
                barColor: '#ff8acc',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#ff8acc5a',
                lineCap: 'circle',
                animate: 3000
            });
        });
</script>
@endpush
