@if (isset($fields) && count($fields) > 0)
    @foreach ($kyc_fields as $item)
        @if ($item->type == 'select')
            <div class="personal-details pb-5">
                <div class="col-lg-12 form-group">
                    <label for="{{ $item->name }}" class="title">
                        {{ $item->label }}
                        @if ($item->required == true)
                            <span class="">*</span>
                        @else
                            <span class="">( {{ __('Optional') }} )</span>
                        @endif
                    </label>
                    <select name="{{ $item->name }}" id="{{ $item->name }}" class="nice-select">
                        <option selected value="">{{ __('Identity Type') }}</option>
                        @foreach ($item->validation->options as $innerItem)
                            <option value="{{ $innerItem }}">{{ $innerItem }}</option>
                        @endforeach
                    </select>
                    @error($item->name)
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        {{-- @elseif ($item->type == 'file') --}}
            {{-- <div class="col-xl-6 col-lg-6 col-md-6 form-group">
                <label>
                    {{ __($item->label) }}
                    @if ($item->required == true)
                        <span class="">*</span>
                    @else
                        <span class="">( {{ __('Optional') }} )</span>
                    @endif
                </label>
                <div class="file-holder-wrapper">
                    <input type="{{ $item->type }}" class="file-holder" name="{{ $item->name }}" id="fileUpload"
                        data-height="130" accept="image/*" data-max_size="20" data-file_limit="15" multiple
                        value="{{ old($item->name) }}">
                </div>
            </div> --}}
        @elseif ($item->type == 'file')
    <div class="col-xl-6 col-lg-6 col-md-6 form-group">
        <label>
            {{ __($item->label) }}
            @if ($item->required == true)
                <span class="text--danger">*</span>
            @else
                <span class="text-muted">( {{ __('Optional') }} )</span>
            @endif
        </label>

        {{-- Webcam UI --}}
        <div class="webcam-area mb-2">
            <video id="webcam_{{ $item->name }}" class="d-none border" autoplay playsinline width="300" height="200"></video>
            <canvas id="canvas_{{ $item->name }}" class="d-none" width="300" height="200"></canvas>
        </div>

        {{-- Hidden file input --}}
        <input type="file" 
               class="d-none file-input" 
               name="{{ $item->name }}" 
               id="file_input_{{ $item->name }}" 
               accept="image/*" 
               @if ($item->required) required @endif>

        {{-- Trigger button --}}
        <button type="button" class="btn--base capture-btn" data-name="{{ $item->name }}">
            {{ __('Capture from Webcam') }}
        </button>

        {{-- Optional error display --}}
        @error($item->name)
            <span class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

        @elseif ($item->type == 'text')
            <div class="col-lg-6 form-group">
                @include('admin.components.form.input', [
                    'label' => $item->label,
                    'name' => $item->name,
                    'type' => $item->type,
                    'value' => old($item->name),
                ])
            </div>
        @elseif ($item->type == 'textarea')
            <div class="col-lg-6 form-group">
                @include('admin.components.form.textarea', [
                    'label' => $item->label,
                    'name' => $item->name,
                    'value' => old($item->name),
                ])
            </div>
        @endif
    @endforeach
@endisset
