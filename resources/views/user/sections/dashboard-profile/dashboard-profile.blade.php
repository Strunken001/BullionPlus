<section class="profile-section ptb-80">
    <div class="container">
        <div class="profile-section-area">
            <div class="row mb-30-none align-items-center">
                <div class="col-xl-7 col-lg-8 col-md-12 col-sm-12 mb-30">
                    <div class="user-profile">
                        <div class="user-account-details">
                            <div class="user-balance">
                                <div class="balance-area">
                                    <label>{{ __(get_amount($user_wallet->balance)) }}</label>
                                    <label class="text--base pt-1">{{ __(get_default_currency_code()) }}</label>
                                </div>
                            </div>
                            <div class="user-account">
                                <label>{{ __('Welcome back') }},
                                    <span><b>{{ __(auth()->user()->username) }}</b></span></label>
                                <h3 class="title">{{ __(auth()->user()->full_mobile) }}</h3>
                                <a href="{{ setRoute('user.purchase.history') }}"><i class="las la-history"></i>
                                    {{ __('Purchase History') }}</a>
                                <a href="{{ setRoute('user.recharge.history') }}"><i class="las la-history"></i>
                                    {{ __('Recharge History') }}</a>
                            </div>
                        </div>
                        <div class="reacharge-opestion">
                            <form action="{{ setRoute('user.recharge.submit.amount') }}" method="POST">
                                @csrf
                                <input type="number" name="amount" class="form--control" placeholder="{{ __('Enter Amount') }}">
                                <div class="reacharge-amount">
                                    <button type="submit" class="btn--base">{{ __('Add Money') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="quick-reacharge">
                            <div class="quick-recharge-tag">
                                <label>{{ __('Quick Add Money') }} :</label>
                            </div>
                            <div class="quick-reacharge-amount">
                                @forelse ($quick_bttns->buttons->items ?? [] as $bttn)
                                <div class="recharge-amount">
                                    <input type="radio" name="q-recharge" data-recharge-value="{{ $bttn->amount }}" class="hide-input"
                                        id="recharge-{{ $loop->iteration }}">
                                    <label for="recharge-{{ $loop->iteration }}" class="recharge--amount">
                                        <p>{{ __($bttn->amount) }} {{ get_default_currency_code() }}</p>
                                    </label>
                                </div>
                                @empty

                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-4 col-md-12 col-sm-12 mb-30">
                    <div class="user-balance-details">
                        <div class="row mb-20-none">
                            <div class="col-lg-12 mb-20">
                                <div class="available-balance">
                                    <div class="left">
                                        <span>{{ $mobile_topup_count }}</span>
                                        <p>{{ __('Total Mobile Topup') }}</p>
                                    </div>
                                    <div class="right">
                                        <i class="las la-phone-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="available-balance">
                                    <div class="left">
                                        <span>{{$giftcard_count }}</span>
                                        <p>{{ __('Total Giftcard') }}</p>
                                    </div>
                                    <div class="right">
                                        <i class="las fas fa-gift"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="available-balance">
                                    <div class="left">
                                        <span>{{ __($add_money_count) }}</span>
                                        <p>{{ __('Total Add Money') }}</p>
                                    </div>
                                    <div class="right">
                                        <i class="las fas fa-wallet"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('script')
    <script>
        $(function() {
            var $rechargeInput = $('[name=amount]');
            $('[name=q-recharge]').on('click', function() {
                $rechargeInput.val($(this).data('recharge-value'));
            });
        });
    </script>
@endpush
