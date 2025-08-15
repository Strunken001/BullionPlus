@if (isset($label))
    @php
        $for_id = preg_replace('/[^A-Za-z0-9\-]/', '', strip_tags(Str::lower($label)));
    @endphp
    <label for="{{ $for_id ?? '' }}">
        {!! $label !!}
        @if (!empty($required) && $required)
            <span class="text--danger">*</span> {{-- show asterisk in red --}}
        @endif
        @isset($label_after)
            {!! $label_after !!}
        @endisset
    </label>
@endif

<input type="{{ $type ?? 'text' }}"
       placeholder="{{ $placeholder ?? 'Type Here...' }}"
       name="{{ $name ?? '' }}"
       class="form--control {{ $class ?? '' }} @error($name ?? false) is-invalid @enderror"
       {{ $attribute ?? '' }}
       value="{{ $value ?? '' }}"
       @isset($data_limit) data-limit="{{ $data_limit }}" @endisset
       @if (!empty($required) && $required) required @endif>

@error($name ?? false)
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
