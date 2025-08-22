{{-- Profile Section Blade --}}

<section class="dashboard-profile-section ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-10">
                <form method="POST" action="{{ setRoute('user.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="dashboard-profile-area">
                        <div class="user-profile">
                            <div class="preview-thumb profile-thumb">
                                @if (auth()->user()->image)
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview bg_img"
                                            data-background="{{ get_image(auth()->user()->image, 'user-profile') }}">
                                        </div>
                                    </div>
                                @else
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview bg_img"
                                            data-background="{{ asset('public/frontend/images/element/user.png') }}">
                                        </div>
                                    </div>
                                @endif
                                <div class="avatar-edit">
                                    <input type='file' class="profilePicUpload" name="image" id="profilePicUpload2"
                                        accept=".png, .jpg, .jpeg">
                                    <label for="profilePicUpload2"><i class="las la-upload"></i></label>
                                </div>
                            </div>
                            <div class="user-name">
                                <h4>{{ __(auth()->user()->username) }}</h4>
                                <p><i class="las la-envelope"></i> {{ __(auth()->user()->email) }}</p>
                            </div>
                        </div>
                        <div class="profile-form-area">
                            <div class="row mb-10-none">
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-10">
                                    <label>{{ __('First Name') }}</label>
                                    <input type="text" class="form--control"
                                        value="{{ __(auth()->user()->firstname) }}" name="firstname">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-10">
                                    <label>{{ __('Last Name') }}</label>
                                    <input type="text" class="form--control"
                                        value="{{ __(auth()->user()->lastname) }}" name="lastname">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-10">
                                    <label>{{ __('Phone Number') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-text phone-code">+{{ auth()->user()->mobile_code }}
                                        </div>
                                        <input class="phone-code" type="hidden" name="phone_code"
                                            value="{{ auth()->user()->mobile_code }}" disabled/>
                                        <input type="number" class="form--control"
                                            placeholder="{{ __('enter Phone Number') }}" name="mobile"
                                            value="{{ __(auth()->user()->mobile) }}" disabled>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-10">
                                    <label>{{ __('Email Address') }}</label>
                                    <input type="email" class="form--control" value="{{ __(auth()->user()->email) }}"
                                        name="email" disabled>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 mb-10">
                                    <label>{{ __('Country') }}</label>
                                    <select class="form--control select-2 select2-auto-tokenize country-select"
                                        data-placeholder="Select Country" name="country"
                                        data-old="{{ old('country', auth()->user()->address->country ?? '') }}"
                                        name="country">
                                    </select>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 mb-10">
                                    <label>{{ __('Address') }}</label>
                                    <input type="text" class="form--control" value="{{ auth()->user()->address->address ?? '' }}"
                                        name="address" placeholder="Enter Address">
                                </div>
                            </div>
                            <div class="save-btn pt-4">
                                <button type="submit" class="btn--base w-100">{{ __('Update Now') }}</button>
                            </div>
                            <div class="save-btn pt-4">
                                <a class="delete-btn text-white btn--danger w-100" href="javascript:void(0)">{{ __('Delete Profile') }}</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>


@push('script')
    <script>
        $(".delete-btn").click(function() {
            var actionRoute = "{{ setRoute('user.profile.delete') }}";
            var target = 1;
            var message = `{{ __('Are you sure to') }} <strong>{{ __('Delete') }}</strong> {{ __('your profile') }}`;
            openAlertModal(actionRoute, target, message, "{{ __('Delete') }}", "GET");
        });
    </script>
@endpush
