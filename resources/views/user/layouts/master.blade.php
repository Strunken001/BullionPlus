<!DOCTYPE html>
<html lang="{{ get_default_language_code() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($page_title) ? __($page_title) : __('Dashboard') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    @include('partials.header-asset')

    @stack('css')
</head>

<body @if ($defualt === 'ar') class="rtl" @endif>


    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Preloader
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    {{-- <div id="preloader"></div> --}}
    {{-- @include('frontend.partials.preloader') --}}
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Preloader
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Dashboard
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


    {{-- @include('user.partials.side-nav') --}}

    <div class="section-wrapper">
        <div class="inner-wrapper">
            @include('user.partials.top-nav')
            @yield('content')
        </div>
        @include('user.partials.footer')
    </div>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Dashboard
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


    @include('partials.footer-asset')
    @include('admin.partials.notify')
    @include('user.partials.push-notification')

    @stack('script')

    <script>
        function openAlertModal(URL, target, message, actionBtnText = "Remove", method = "DELETE") {
            if (URL == "" || target == "") {
                return false;
            }

            if (message == "") {
                message = "Are you sure to delete ?";
            }
            var method = `<input type="hidden" name="_method" value="${method}">`;
            openModalByContent({
                    content: `<div class="card modal-alert border-0">
                        <div class="card-body">
                            <form method="POST" action="${URL}">
                                <input type="hidden" name="_token" value="${laravelCsrf()}">
                                ${method}
                                <div class="head mb-3">
                                    ${message}
                                    <input type="hidden" name="target" value="${target}">
                                </div>
                                <div class="foot d-flex align-items-center justify-content-between">
                                    <button type="button" class="modal-close btn btn--info btn--base">{{ __('Close') }}</button>
                                    <button type="submit" class="alert-submit-btn btn btn--base bg--danger btn-loading">${actionBtnText}</button>
                                </div>
                            </form>
                        </div>
                    </div>`,
                },

            );
        }
    </script>

</body>

</html>
