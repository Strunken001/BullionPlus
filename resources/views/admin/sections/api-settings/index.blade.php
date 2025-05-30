@extends('admin.layouts.master')

@push('css')

@endpush

@section('page-title')
    @include('admin.components.page-title',['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("admin.dashboard"),
        ]
    ], 'active' => __($page_title)])
@endsection

@section('content')
    <div class="custom-card">
        <div class="card-header">
            <h6 class="title">{{$page_title}}</h6>
        </div>
        <div class="card-body">
            <form class="card-form" action="{{ setRoute('admin.api.settings.generate.keys') }}" method="POST">
                @csrf
                <div class="row mb-10-none">
                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8 form-group">
                        <label>{{ __("API Key") }}</label>
                        <div class="input-group mb-3">
                            <input type="password" class="form--control" id="apiKey" value="{{ @$data->api_key }}" readonly>
                            <button type="button" onclick="toggleVisibility('apiKey', this)">
                                <i class="las la-eye"></i>
                            </button>
                            <button type="button" onclick="copyToClipboard('apiKey')">
                                <i class="las la-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8 form-group">
                        <label>{{ __("API Secret") }}</label>
                        <div class="input-group mb-3">
                            <input type="password" class="form--control" id="apiSecret" value="{{ @$data->secret_key }}" readonly>
                            <button type="button" onclick="toggleVisibility('apiSecret', this)">
                                <i class="las la-eye"></i>
                            </button>
                            <button type="button" onclick="copyToClipboard('apiSecret')">
                                <i class="las la-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group">
                        @include('admin.components.button.form-btn',[
                            'class'         => "w-100 btn-loading",
                            'text'          => __("Generate Keys"),
                        ])
                    </div>
                </div>
            </form>
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


