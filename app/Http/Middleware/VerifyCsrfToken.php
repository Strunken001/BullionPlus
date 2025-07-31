<?php

namespace App\Http\Middleware;

use Closure;

use App\Constants\PaymentGatewayConst;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'user/username/check',
        'user/check/email',
        'user/recharge/success/response/' . PaymentGatewayConst::SSLCOMMERZ,
        'user/recharge/cancel/response/' . PaymentGatewayConst::SSLCOMMERZ,
        'user/recharge/success/response/' . PaymentGatewayConst::RAZORPAY,
        'user/recharge/cancel/response/' . PaymentGatewayConst::RAZORPAY,
    ];

    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session expired. Please login and try again.'
                ], 419);
            }

            return redirect()
                ->route('user.login')
                ->withInput($request->except('_token'))
                ->withErrors([
                    'message' => 'Your session has expired. Please log in again.',
                ]);
        }
    }
}
