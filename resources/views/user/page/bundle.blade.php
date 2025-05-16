@extends('user.layouts.master')
@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp

@section('content')
@include('user.sections.data-bundle.index')
@endsection
