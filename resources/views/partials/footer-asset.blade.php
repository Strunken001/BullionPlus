<!-- jquery -->
<script src="{{ asset('frontend/js/jquery-3.6.0.js') }}"></script>
<!-- bootstrap js -->
<script src="{{ asset('frontend/js/bootstrap.bundle.js') }}"></script>
<!-- bootstrap js -->
<script src="{{ asset('frontend/js/swiper.js') }}"></script>
<!-- lightcase js-->
<script src="{{ asset('frontend/js/lightcase.js') }}"></script>
<!-- odometer js -->
<script src="{{ asset('frontend/js/odometer.js') }}"></script>
<!-- viewport js -->
<script src="{{ asset('frontend/js/viewport.jquery.js') }}"></script>
<!-- AOS js -->
<script src="{{ asset('frontend/js/aos.js') }}"></script>
<!-- smooth scroll js -->
<script src="{{ asset('frontend/js/smoothscroll.js') }}"></script>
<!-- nice select js -->
<script src="{{ asset('frontend/js/jquery.nice-select.js') }}"></script>
<!-- select2 -->
<script src="{{ asset('frontend/js/select2.js') }}"></script>

<script src="{{ asset('backend/library/popup/jquery.magnific-popup.js') }}"></script>
<!-- main -->
<script src="{{ asset('frontend/js/main.js') }}"></script>
<!-- Country code -->
<script src="{{ asset('frontend/js/country-code.js')}}"></script>
@php
    $routeCheck =  request()->routeIs('user.login');
@endphp
<script>
    var routeCheck = "{{ $routeCheck }}";
    if(routeCheck){
         // Function to update the display based on the selected login option
    function updateLoginForm() {
        const otpForm = document.getElementById('otp-login-form');
        const passwordForm = document.getElementById('password-login-area');
        const otpRadio = document.getElementById('radio-one');

        if (otpRadio.checked) {
            otpForm.style.display = 'block';
            passwordForm.style.display = 'none';
        } else {
            otpForm.style.display = 'none';
            passwordForm.style.display = 'block';
        }
    }

    // Add event listeners to the radio buttons
    document.getElementById('radio-one').addEventListener('change', updateLoginForm);
    document.getElementById('radio-two').addEventListener('change', updateLoginForm);

    // Initial call to set the correct display on page load
    updateLoginForm();
    }

</script>
<script>
    $("select[name=lang_switcher]").change(function() {
        var selected_value = $(this).val();
        var submitForm =
            `<form action="{{ route('frontend.languages.switch') }}" id="local_submit" method="POST"> @csrf <input type="hidden" name="target" value="${$(this).val()}" ></form>`;
        $("body").append(submitForm);
        $("#local_submit").submit();
    });
</script>
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
