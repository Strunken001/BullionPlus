@extends('user.layouts.master')

@push('css')
@endpush

@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp

@section('content')
    <div class="table-content">
        <div class="container">
            <div class="row mb-20-none justify-content-center">
                <div class="col-xl-8 col-lg-10 mb-20">
                    <div class="header-title ptb-60">
                        @if ($basic_settings->kyc_verification == true && isset($kyc_data) && $kyc_data != null && $kyc_data->fields != null)
                            <div class="custom-card mt-30">
                                <div class="dashboard-kyc-wrapper kyc-information-submit">
                                    <div class="kyc-form-header">
                                        <h4 class="title">{{ __('KYC Information') }} &nbsp; <span
                                            class="{{ auth()->user()->kycStringStatus->class }}">{{ __(auth()->user()->kycStringStatus->value) }}</span>
                                        </h4>
                                        @if (auth()->user()->kyc_verified == global_const()::REJECTED)
                                            <button class="btn--base text-center" id="re-submit">
                                                {{ __('Re-submit') }}
                                            </button>
                                        @endif
                                    </div>
                                    @if (auth()->user()->kyc_verified == global_const()::REJECTED)
                                        <div class="rejected">
                                            <div class="rejected-title">
                                                <h5 class="title">{{ __('Rejected Reason') }} :</h5>
                                            </div>
                                            <div class="rejected-reason">
                                                {{ auth()->user()->kyc->reject_reason ?? '' }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if (auth()->user()->kyc_verified == global_const()::PENDING)
                                        <div class="pending text--warning kyc-text">
                                            {{ __('Your KYC information is submited. Please wait for admin confirmation. When you are KYC verified you will show your submited information here.') }}
                                        </div>
                                    @elseif (auth()->user()->kyc_verified == global_const()::APPROVED)
                                        @include('user.components.profile.kyc')
                                    @elseif (auth()->user()->kyc_verified == global_const()::REJECTED)
                                        @include('user.components.profile.kyc')
                                        <div class="custom-card mt-10 d-none" id="kyc-form">
                                            {{-- KYC Submiting form --}}
                                            <form class="card-form" method="POST"
                                                action="{{ setRoute('user.kyc.submit') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row">
                                                    @include('user.components.generate-kyc-fields', [
                                                        'fields' => $kyc_fields,
                                                    ])
                                                </div>
                                                <div class="resubmit-btn-area text-center pt-30">
                                                        <button type="submit" class="btn--base text-center">
                                                            {{ __('Submit') }}
                                                        </button>
                                                        <button type="button" id="re-cencel"
                                                            class="btn--base text-center">
                                                            {{ __('Cancel') }}
                                                        </button>
                                                </div>
                                            </form>
                                        </div>
                                    @else
                                        <div class="custom-card mt-10">
                                            {{-- KYC Submiting form --}}
                                            <form class="card-form" method="POST"
                                                action="{{ setRoute('user.kyc.submit') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row">
                                                    @include('user.components.generate-kyc-fields', [
                                                        'fields' => $kyc_fields,
                                                    ])
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn--base w-100 text-center mt-5">
                                                        {{ __('Submit') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(function() {
            var $reSubBttn = $('#re-submit'),
                $cencelBttn = $('#re-cencel'),
                $kycForm = $('#kyc-form'),
                $kycDetail = $('#kyc-detail');

            $reSubBttn.on('click', function() {
                $kycForm.removeClass('d-none');
                $kycDetail.add($reSubBttn).addClass('d-none');
            });

            $cencelBttn.on('click', function() {
                $kycDetail.add($reSubBttn).removeClass('d-none');
                $kycForm.addClass('d-none');
            });
        });
    </script>
@endpush
