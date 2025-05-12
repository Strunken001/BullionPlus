@extends('user.layouts.master')

@push('css')
@endpush

@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp

@section('breadcrumb')
    @include('user.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('user.dashboard'),
            ],
        ],
        'active' => __('Support Tickets'),
    ])
@endsection

@section('content')
    <section class="add-tickets-section ptb-60">
        <div class="container">
            <div class="add-tickets">
                <div class="row mb-20-none">
                    <div class="col-xl-12 col-lg-12 mb-20">
                        <div class="custom-card mt-10">
                            <div class="dashboard-header-wrapper">
                                <h4 class="title">{{ __('Add New Ticket') }}</h4>
                            </div>
                            <div class="card-body">
                                <form class="card-form" action="{{ route('user.support.ticket.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 form-group">
                                            <label>{{ __('Subject') }}<span>*</span></label>
                                            <input type="text" class="form--control" placeholder="{{ __('Enter Subject') }}..."
                                                name="subject">
                                        </div>
                                        <div class="col-xl-12 col-lg-12 form-group">
                                            <label>{{ __('Description') }} <span
                                                    class="text--base">*</span></label>
                                            <textarea class="form--control" placeholder="{{ __('Write Here') }}…" name="description"></textarea>
                                        </div>
                                        <div class="col-xl-12 col-lg-12 form-group">
                                            <label>{{ __('Attachments') }} <span>({{ __('Optional') }})</span></label>
                                            <div class="file-holder-wrapper">
                                                <input type="file" class="file-holder" name="attachment[]"
                                                    id="fileUpload" data-height="130" accept="image/*" data-max_size="20"
                                                    data-file_limit="15" multiple>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="button pt-3">
                                        <button type="submit" class="btn btn--base w-100">{{ __('Add New') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection




@push('script')
    <script></script>
@endpush
