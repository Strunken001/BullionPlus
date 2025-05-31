@extends('user.layouts.master')
@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp

@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("API Settings")])
@endsection

@section('content')
@include('user.sections.api-settings.index')
@endsection
