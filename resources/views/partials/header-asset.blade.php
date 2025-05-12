<!-- fontawesome css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/fontawesome-all.css') }}">
<!-- bootstrap css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/bootstrap.css') }}">
<!-- favicon -->
<link rel="shortcut icon" href="{{ get_fav($basic_settings) }}" type="image/x-icon">
<!-- swipper css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/swiper.css') }}">
<!-- lightcase css links -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/lightcase.css') }}">
<!-- AOS css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/aos.css') }}">
<!-- odometer css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/odometer.css') }}">
<!-- animate.css -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/animate.css') }}">
<!-- line-awesome-icon css -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/line-awesome.css') }}">
<!-- nice-select -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/nice-select.css') }}">
<!-- select2 css -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/select2.css') }}">

<link rel="stylesheet" href="{{ asset('public/backend/library/popup/magnific-popup.css') }}">
<!-- main style css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/style.css') }}">
<!-- Country code -->
<link rel="stylesheet" href="{{ asset('public/frontend/css/country-code.css') }}">

@php
    $color = @$basic_settings->base_color ?? '#000000';
@endphp

<style>
    :root {
        --primary-color: {{ $color }};
    }
</style>
