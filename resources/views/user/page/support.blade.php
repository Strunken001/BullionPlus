@extends('user.layouts.master')
@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp
@push('css')
@endpush

@section('breadcrumb')
    @include('user.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('user.dashboard'),
            ],
        ],
        'active' => __('Profile'),
    ])
@endsection

@include('user.sections.support-ticket.index')

@push('script')
    <script>
        $(".active-deactive-btn").click(function(){
            var actionRoute =  "{{ setRoute('user.security.google.2fa.status.update') }}";
            var target      = 1;
            var btnText = $(this).text();
            var sureText = '{{ __("Are you sure to") }}';
            var lastText = '{{ __("2 factor authentication (Powered by google)") }}';
            var message     = `${sureText} <strong>${btnText}</strong> ${lastText}?`;
            openAlertModal(actionRoute,target,message,btnText,"POST");
        });
    </script>
@endpush
