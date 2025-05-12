<div class="row" id="kyc-detail">
    @forelse (auth()->user()->kyc->data ?? [] as $item)
        @if ($item->type == 'file')
            <div class="col-xl-6 col-lg-6 col-md-6 form-group">
                @php
                    $file_link = get_file_link('kyc-files', $item->value);
                @endphp
                <span class="kyc-title">{{ __($item->label) }}:</span>
                @if (its_image($item->value))
                    <div class="information-img">
                        <img src="{{ $file_link }}" alt="{{ $item->label }}">
                    </div>
                @else
                    <span class="text--danger">
                        @php
                            $file_info = get_file_basename_ext_from_link($file_link);
                        @endphp
                        <a href="{{ setRoute('file.download', ['kyc-files', $item->value]) }}">
                            {{ Str::substr($file_info->base_name ?? '', 0, 20) . '...' . $file_info->extension ?? '' }}
                        </a>
                    </span>
                @endif
            </div>
        @elseif ($item->type == 'select' || $item->type == 'text')
            <div class="personal-details pb-5">
                <div class="col-lg-12 col-md-12">
                    <div class="kyc-information-data">
                        <h4 class="title">{{ __($item->label) }}: </h4>
                        <p class="type">{{ $item->value }}</p>
                    </div>
                </div>
            </div>
        @endif
    @empty
    @endforelse
</div>
