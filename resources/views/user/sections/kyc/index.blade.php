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
                                    @if (!auth()->user()->has_done_liveness)
                                        <div class="text-center mt-3">
                                            <button type="submit" class="btn--base">
                                                <a href="{{config('app.react_liveness_url')}}?firstname={{auth()->user()->firstname}}&lastname={{auth()->user()->lastname}}&email={{auth()->user()->email}}">
                                                    {{ __('Start Liveness Check') }}
                                                </a>
                                            </button>
                                        </div>
                                    @else
                                        <div class="card-body">
                                            @if (auth()->user()->kyc_verified == global_const()::PENDING)
                                                <div class="pending text--warning kyc-text">
                                                    {{ __('Your KYC information is submitted. Please wait for admin confirmation. When you are KYC verified, your submitted information will be shown here.') }}
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

       $(function () {
        $('.webcam-btn').on('click', function () {
            const name = $(this).data('name');
            const video = document.getElementById('webcam_' + name);
            const canvas = document.getElementById('canvas_' + name);
            const fileInput = document.getElementById('file_input_' + name);

            const webcamBtn = $(this);
            const captureBtn = $('.capture-btn[data-name="' + name + '"]');
            const retakeBtn = $('.retake-btn[data-name="' + name + '"]');

            webcamBtn.addClass('d-none');
            captureBtn.removeClass('d-none');
            retakeBtn.addClass('d-none');
            canvas.classList.add('d-none');
            video.classList.remove('d-none');

            navigator.mediaDevices.getUserMedia({ video: true }).then(function (stream) {
                video.srcObject = stream;
                video.play();

                video.dataset.streamId = stream.id;

                captureBtn.off('click').on('click', function () {
                    const context = canvas.getContext('2d');

                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    canvas.toBlob(function (blob) {
                        const file = new File([blob], name + ".png", { type: "image/png" });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        fileInput.files = dataTransfer.files;
                    }, 'image/png');

                    video.classList.add('d-none');
                    canvas.classList.remove('d-none');
                    captureBtn.addClass('d-none');
                    retakeBtn.removeClass('d-none');

                    stream.getTracks().forEach(track => track.stop());
                });

                retakeBtn.off('click').on('click', function () {
                    retakeBtn.addClass('d-none');
                    webcamBtn.click();
                });
            }).catch(function (err) {
                alert("Could not access the camera. Please allow permission.");
                console.error(err);
            });
        });
    });
    </script>
@endpush
