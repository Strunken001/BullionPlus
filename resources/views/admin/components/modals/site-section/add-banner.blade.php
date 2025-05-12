<div id="bnr-add" class="mfp-hide large">
    <div class="modal-data">
        <div class="modal-header px-0">
            <h5 class="modal-title">{{ __('Add New Item') }}</h5>
        </div>
        <div class="modal-form-data">
            <form class="modal-form" method="POST"
                action="{{ setRoute('admin.setup.sections.section.item.store', $slug) }}" enctype="multipart/form-data">
                @csrf
                <div class="row justify-content-center mb-10-none">
                    <div class="col-xl-4 col-lg-4 form-group">
                        @include('admin.components.form.input-file', [
                            'label' => __('Image') . ':',
                            'name' => 'image',
                            'class' => 'file-holder',
                            'old_files_path' => files_asset_path('site-section'),
                            'old_files' => $data->value->image ?? '',
                        ])
                    </div>
                    <div class="col-xl-8 col-lg-8">
                        <div class="language-tab">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    @foreach ($languages as $item)
                                        <button class="nav-link @if (get_default_language_code() == $item->code) active @endif"
                                            id="modal-{{ $item->name }}-tab" data-bs-toggle="tab"
                                            data-bs-target="#modal-{{ $item->name }}" type="button" role="tab"
                                            aria-controls="modal-{{ $item->name }}"
                                            aria-selected="true">{{ $item->name }}</button>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                @foreach ($languages as $item)
                                    @php
                                        $lang_code = $item->code;
                                    @endphp
                                    <div class="tab-pane @if (get_default_language_code() == $item->code) fade show active @endif"
                                        id="modal-{{ $item->name }}" role="tabpanel"
                                        aria-labelledby="modal-{{ $item->name }}-tab">
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Heading'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_heading',
                                                'value' => old(
                                                    $item->code . '_heading',
                                                    $data->value->language->$lang_code->heading ?? ''),
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Sub Heading'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_sub_heading',
                                                'value' => old(
                                                    $item->code . '_sub_heading',
                                                    $data->value->language->$lang_code->sub_heading ?? ''),
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Button Name'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_button_name',
                                                'value' => old(
                                                    $item->code . '_button_name',
                                                    $data->value->language->$lang_code->button_name ?? ''),
                                            ])
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-xl-12 col-lg-12 form-group">
                                    <label for="">{{ __('Button Link') }}*</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">{{ url('/') }}/</span>
                                        <input type="text" class="form--control" name="button_link"
                                            value="{{ old('button_link', $data->value->button_link ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                        <button type="button" class="btn btn--danger modal-close">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn--base">{{ __('Add') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
