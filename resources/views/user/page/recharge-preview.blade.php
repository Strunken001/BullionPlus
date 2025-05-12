@extends('user.layouts.master')
@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp
@push('css')
@endpush

@section('content')
    @include('user.sections.recharge.recharge-preview')
@endsection

@push('script')
    
@endpush
