{{-- <table class="table table-striped custom-table">
    <thead>
        <tr>
            <th scope="col">{{ __('Volume') }}</th>
            <th scope="col">{{ __('Price') }}</th>
            <th scope="col">{{ __('Date') }}</th>
        </tr>
    </thead>
    <tbody id="data-container-{{ $type }}">
        @if ($data->count())
            @forelse ($data ?? [] as $history)
                <tr>
                    <th @if ($loop->iteration % 2 === 0) scope="row" @endif>
                        {{ $history->request_amount }} {{ $history->request_currency }}
                    </th>
                    <td>{{ $history->total_payable }} {{ get_default_currency_code() }}</td>
                    <td>{{ date('Y-m-d', strtotime($history->created_at)) }}</td>
                </tr>
            @empty
                <tr class="text-center">
                    <td colspan="3">{{ __('Nothing to show yet') }}</td>
                </tr>
            @endforelse
        @else
        @endif
    </tbody>
</table>

<div class="pagination-links" id="pagination-{{ $type }}">
    {{ $data->links(null,[],null, null, $type) }}
</div> --}}
