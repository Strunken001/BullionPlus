@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header text-white rounded-top-4">
            <h3 class="my-3">{{ $page_title }}</h3>
        </div>
        <div class="card-body p-4">
            <form action="{{ setRoute('user.api.settings.generate.keys') }}" method="POST">
                @csrf
                <div class="row justify-content-center gy-4">
                    {{-- API Key --}}
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">{{ __("API Key") }}</label>
                        <div class="input-group">
                            <input type="password" class="form-control rounded-start" id="apiKey" value="{{ @$data->api_key }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('apiKey', this)">
                                <i class="las la-eye"></i>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('apiKey')">
                                <i class="las la-copy"></i>
                            </button>
                        </div>
                    </div>

                    {{-- API Secret --}}
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">{{ __("API Secret") }}</label>
                        <div class="input-group">
                            <input type="password" class="form-control rounded-start" id="apiSecret" value="{{ @$data->secret_key }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('apiSecret', this)">
                                <i class="las la-eye"></i>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('apiSecret')">
                                <i class="las la-copy"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Button --}}
                    <div class="col-md-6 text-center">
                        @include('admin.components.button.form-btn',[
                            'class' => "btn btn-primary btn-lg w-100 btn-loading mt-2 rounded-pill",
                            'text'  => __("Generate Keys"),
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('script')
<script>
    function copyToClipboard(id) {
        const input = document.getElementById(id);
        
        if (input.type === "password") {
            return;
        }

        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand("copy");
        alert("Copied: " + input.value);
    }

    function toggleVisibility(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('la-eye');
            icon.classList.add('la-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('la-eye-slash');
            icon.classList.add('la-eye');
        }
    }
</script>
@endpush

@push('css')
<style>
    .input-group .btn i {
        font-size: 1.2rem;
    }
    input.form-control:read-only {
        background-color: #f9f9f9;
    }

</style>
@endpush


