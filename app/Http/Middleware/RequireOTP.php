<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;

class RequireOTP
{
    /**
     * The response factory instance.
     *
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $responseFactory;

    /**
     * The URL generator instance.
     *
     * @var \Illuminate\Contracts\Routing\UrlGenerator
     */
    protected $urlGenerator;

    /**
     * The otp timeout.
     *
     * @var int
     */
    protected $otpTimeout;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Routing\ResponseFactory  $responseFactory
     * @param  \Illuminate\Contracts\Routing\UrlGenerator  $urlGenerator
     * @param  int|null  $otpTimeout
     * 
     * @return void
     */
    public function __construct(ResponseFactory $responseFactory, UrlGenerator $urlGenerator, $otpTimeout = null)
    {
        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
        $this->otpTimeout = $otpTimeout ?: config('auth.otp_timeout', 3600);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * 
     * @return mixed
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if ($this->shouldConfirmOTP($request)) {
            if ($request->expectsJson()) {
                return $this->responseFactory->json([
                    'message' => 'OTP confirmation required.',
                ], 423);
            }

            return $this->responseFactory->redirectGuest(
                $this->urlGenerator->route($redirectToRoute ?? '2fa.otp')
            );
        }

        return $next($request);
    }

    /**
     * Determine if the confirmation timeout has expired.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return bool
     */
    protected function shouldConfirmOTP($request)
    {
        $confirmedAt = time() - $request->session()->get('auth.otp_confirmed_at', 0);

        // \Log::debug(self::class, ["{$confirmedAt} > {$this->otpTimeout}"]);

        return $confirmedAt > $this->otpTimeout;
    }
}
