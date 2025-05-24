@extends('frontend.layouts.master')
@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp
@push('css')
@endpush

@section('content')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Services Section
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

    @include('frontend.sections.pricing-section')


@endsection


@push('script')
@endpush
