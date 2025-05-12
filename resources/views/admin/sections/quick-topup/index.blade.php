@extends('admin.layouts.master')

@push('css')
    <style>
        .switch-toggles {
            margin-left: auto;
        }
    </style>
@endpush

@section('page-title')
    @include('admin.components.page-title', ['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('admin.dashboard'),
            ],
        ],
        'active' => __('Quick Topup'),
    ])
@endsection

@section('content')
    <div class="table-area mt-15">
        <div class="table-wrapper">
            <div class="table-header justify-content-end">
                <div class="table-btn-area">
                    <a href="#amount-add" class="btn--base modal-btn"><i class="fas fa-plus me-1"></i>
                        {{ __('Add Button') }}</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>{{ __('#SL') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($buttons->buttons->items ?? [] as $key => $item)
                            <tr data-item="{{ json_encode($item) }}">
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td><span class="text--dark">{{ $item->amount ?? '' }}</span>
                                </td>
                                <td>
                                    <button class="btn btn--base edit-modal-button"><i
                                            class="las la-pencil-alt"></i></button>
                                    <button class="btn btn--base btn--danger delete-modal-button"><i
                                            class="las la-trash-alt"></i></button>
                                </td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty', ['colspan' => 6])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.components.modals.site-section.add-topup')

    {{-- edit modal --}}
    <div id="amount-edit" class="mfp-hide large">
        <div class="modal-data">
            <div class="modal-header px-0">
                <h5 class="modal-title">{{ __('Edit Item') }}</h5>
            </div>
            <div class="modal-form-data">
                <form class="modal-form" method="POST" action="{{ setRoute('admin.short.topup.update') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="target" value="{{ old('target') }}">
                    <div class="row mb-10-none mt-3">
                        <div class="language-tab">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="form-group">
                                    <div class="form-group">
                                        @include('admin.components.form.input', [
                                            'label' => __('Amount'),
                                            'label_after' => '*',
                                            'placeholder' => __('Write Here') . '...',
                                            'name' => 'amount_edit',
                                            'value' => old('amount_edit', $data->value->amount ?? ''),
                                            'class' => 'form--control',
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                            <button type="button" class="btn btn--danger modal-close">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn--base">{{ __('Update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        openModalWhenError("amount-add", "#amount-add");
        openModalWhenError("amount-edit", "#amount-edit");

        $(".edit-modal-button").click(function() {
            var oldData = JSON.parse($(this).parents("tr").attr("data-item"));
            var editModal = $("#amount-edit");

            editModal.find("form").first().find("input[name=target]").val(oldData.id);
            editModal.find("input[name=amount_edit]").val(oldData.amount);
            openModalBySelector("#amount-edit");
        });

        $(".delete-modal-button").click(function() {
            var oldData = JSON.parse($(this).parents("tr").attr("data-item"));

            var actionRoute = "{{ setRoute('admin.short.topup.delete') }}";
            var target = oldData.id;
            var message = `{{ __('Are you sure to') }} <strong>{{ __('delete') }}</strong> {{ __('item?') }}`;

            openDeleteModal(actionRoute, target, message);
        });
    </script>
@endpush
