@if (isset($label))
    @php
        $for_id = preg_replace('/[^A-Za-z0-9\-]/', '', Str::lower(strip_tags($label)));
    @endphp
    <label for="{{ $for_id ?? '' }}">
        {!! $label !!}
        @if (!empty($required) && $required)
            <span class="text--danger">*</span>
        @else
            <span class="text-muted">( {{ __('Optional') }} )</span>
        @endif
        @isset($label_after)
            {!! $label_after !!}
        @endisset
    </label>
@endif

<textarea
    class="{{ $class ?? 'form--control' }} @error($name ?? false) is-invalid @enderror"
    placeholder="{{ $placeholder ?? 'Type Here...' }}"
    name="{{ $name ?? '' }}"
    @if (!empty($required) && $required) required @endif
    {{ $attribute ?? '' }}
    @isset($data_limit) data-limit="{{ $data_limit }}" @endisset
>{{ $value ?? '' }}</textarea>

@error($name ?? false)
    <span class="invalid-feedback d-block" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
