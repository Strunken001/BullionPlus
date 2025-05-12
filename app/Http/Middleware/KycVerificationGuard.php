<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Providers\Admin\BasicSettingsProvider;
use App\Constants\GlobalConst;

class KycVerificationGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $basic_settings = BasicSettingsProvider::get();
        if($basic_settings->kyc_verification) {
            $user = auth()->user();
            if($user->kyc_verified != GlobalConst::APPROVED) {
                $smg = __("Please verify your KYC information before any transactional action");
                if($user->kyc_verified === GlobalConst::DEFAULT) {
                    $smg = __('Please submit kyc information!');
                    $type = "error";
                }else if($user->kyc_verified == GlobalConst::PENDING) {
                     $smg = __('Your KYC information is pending. Please wait for admin confirmation.');
                     $type = "warning";
                }elseif($user->kyc_verified == GlobalConst::REJECTED){
                     $smg = __('Your KYC information is rejected. Please submit again your information to admin.');
                     $type = "error";
                }
                if(auth()->guard("web")->check()) {
                    return back()->with([$type??"warning" => [$smg]]);
                }
            }
        }
        return $next($request);
    }
}
