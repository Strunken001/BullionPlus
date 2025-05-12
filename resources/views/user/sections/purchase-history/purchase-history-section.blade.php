<!-- Purchase History -->
<section class="purchase-history-section ptb-80">
    <div class="container">
        <div class="purchase-history-area">
            <div class="dashboard-list-area">
                <div class="dashboard-header-wrapper">
                    <h3 class="title">{{ __('Your Purchase History') }}</h3>
                </div>
            </div>
            <div class="purchase-model-type">
                @forelse ($purchase_type ?? [] as $item)
                    <div class="purchase-type">
                        <input type="radio" name="{{ $item->type }}" class="hide-input" id="{{ $item->type }}">
                        <label class="switch @if ($loop->iteration === 1) switch-active @endif"
                            for="{{ $item->type }}">
                            <p>{{ __($item->type) }}</p>
                        </label>
                    </div>
                @empty
                @endforelse
            </div>
            <div class="history-sections">
                @forelse ($purchase_type ?? [] as $item)
                    <div class="history @if ($loop->iteration != 1) d-none @endif" id="div-{{ $item->type }}">
                        <div class="row">
                            <div class="col-lg-8 col-md-10">
                                <div class="table-responsive">
                                    @php
                                        $data = getHistory($item->type);
                                    @endphp
                                    <table class="table table-striped custom-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ __('Volume') }}</th>
                                                <th scope="col">{{ __('Price') }}</th>
                                                @if ($item->type === 'MOBILE-TOPUP')
                                                    <th scope="col">{{ __('Mobile Number') }}</th>
                                                @endif
                                                <th scope="col">{{ __('Date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="data-container-{{ $item->type }}">
                                            @if ($data->count())
                                                @forelse ($data?? [] as $history)
                                                    <tr>
                                                        <th @if ($loop->iteration % 2 === 0) scope="row" @endif>
                                                            {{ $history->request_amount }}
                                                            {{ $history->request_currency }}</th>
                                                        <td>{{ $history->total_payable }}
                                                            {{ get_default_currency_code() }}</td>
                                                        @if ($item->type === 'MOBILE-TOPUP')
                                                            <td>{{ $history->details->mobile_number }}</td>
                                                        @endif
                                                        <td>{{ date('Y-m-d', strtotime($history->created_at)) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr class="text-center">
                                                        <td @if($item->type === 'MOBILE-TOPUP') colspan="4" @else colspan="3" @endif >{{ __('Nothing to show yet') }}</td>
                                                    </tr>
                                                @endforelse
                                            @else
                                                <tr class="text-center">
                                                    <td @if($item->type === 'MOBILE-TOPUP') colspan="4" @else colspan="3" @endif>{{ __('Nothing to show yet') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    <div class="pagination-links" id="pagination-{{ $item->type }}">
                                        {{ $data->links(null, [], null, null, $item->type) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-warning">
                        {{ __('Nothing to show yet') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

@push('script')
    <script>
        $(document).ready(function() {
            var $bttns = $(".hide-input");
            var $tbls = $(".history");
            var $switch = $(".switch");

            $bttns.each(function() {
                var $input = $(this);
                $input.on('click', function() {
                    var $tbl = $(`#div-${$input.attr('id')}`);
                    $tbls.addClass("d-none");
                    $tbl.toggleClass("d-none");
                    $switch.removeClass("switch-active");
                    $input.siblings().addClass('switch-active');
                });
            });
        });
    </script>
    <script>
        $(document).on('click', '.pagination-links a', function(e) {
            e.preventDefault(); // Prevent default link behavior

            var url = $(this).attr('href'); // Get the URL of the clicked pagination link
            var type = $(this).closest('.pagination-links').attr('id').replace('pagination-',
                ''); // Extract type from ID

            // Perform the AJAX request
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    console.log(url)
                    // Replace the content of the table body and pagination links with the new data
                    $('#data-container-' + type).html($(response).find('#data-container-' + type)
                        .html());
                    $('#pagination-' + type).html($(response).find('#pagination-' + type).html());
                },
                error: function(xhr) {
                    alert('Something went wrong, please try again.');
                }
            });
        });
    </script>
@endpush
