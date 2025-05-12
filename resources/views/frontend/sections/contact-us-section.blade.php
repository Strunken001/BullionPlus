<!-- Contact Section -->

<section class="contact-section ptb-80">
    <div class="container">
       <div class="contact-form">
            <div class="massage-area">
                <div class="row mb-30-none justify-content-center">
                    <div class="col-xl-12 col-lg-12 mb-30">
                        <div class="contact-form-area">
                            <div class="contact-header text-center pb-30">
                                <h2 class="title">{{ __(@$contact_us->value->language->$defualt->heading ?? @$contact_us->value->language->$default_lng->heading) }}</h2>
                            </div>
                            <form action="{{ setRoute('frontend.contact.message.send') }}" class="contact-form" method="POST">
                                @csrf
                                <div class="row justify-content-center mb-10-none">
                                    <div class="col-xl-6 col-lg-6 col-md-6 form-group">
                                        <label>{{ __('Name') }}<span>*</span></label>
                                        <input type="text" class="form--control" placeholder="{{ __('Enter Name') }}..." name="name">
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 form-group">
                                        <label>{{ __('Email') }}<span>*</span></label>
                                        <input type="email" class="form--control" placeholder="{{ __('Enter Email') }}..." name="email">
                                    </div>
                                    <div class="col-xl-12 col-lg-12 form-group">
                                        <label>{{ __('Message') }}<span>*</span></label>
                                        <textarea class="form--control" placeholder="{{ __('Write Here') }}..." name="message"></textarea>
                                    </div>
                                    <div class="col-lg-12 form-group pt-3">
                                        <button type="submit" class="btn--base w-100">{{ __('Send Message') }}</button>
                                    </div>
                                 </div>
                             </form>
                        </div>
                     </div>
                </div>
            </div>
         </div>
    </div>
</section>
