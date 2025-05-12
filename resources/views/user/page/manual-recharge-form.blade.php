@extends('user.layouts.master')

@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp

@push('css')

@endpush

@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("Manual Payment")])
@endsection

@section('content')
    @include('user.sections.manual-recharge.manual-recharge-section')
@endsection
