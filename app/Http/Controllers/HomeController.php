<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Container\Container;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    use RedirectsUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('auth')->except([
            'show2faQrcode',
            'show2faOtp',
            'verify2faOtp',
        ]);
    }

    public function index()
    {
        return view('home');
    }

    public function toogle2fa(Request $request)
    {
        $user = $request->user();

        if($request->has('two_factor_enabled')) {
            return redirect()->route('2fa.qrcode');
        }

        $user->two_factor_enabled = false;
        $user->two_factor_secret = '';
        $user->save();

        return redirect()->back();
    }

    public function show2faQrcode(Request $request)
    {
        $user = $this->getUser($request);

        if($user->two_factor_enabled) {
            // return redirect()->route('home')->with('status', "2FA is already enabled.");
            return redirect()->route('2fa.otp')->with('status', "2FA is already enabled.");
        }

        $google2fa = Container::getInstance()->make('pragmarx.google2fa');

        $two_factor_secret = $google2fa->generateSecretKey();

        $request->session()->put('two_factor_secret', $two_factor_secret);

        $qrcode_svg = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $two_factor_secret,
        );

        return view('2fa.qrcode', [
            'user' => $user,
            'qrcode_svg' => $qrcode_svg, 
            'two_factor_secret' => $two_factor_secret,
        ]);
    }

    public function show2faOtp(Request $request)
    {
        // if($request->user()->two_factor_enabled) {
        //     return redirect()->route('home')->with('status', "2FA is already enabled.");
        // }

        // if(! $request->user()->two_factor_enabled) {
        //     return redirect()->route('2fa.qrcode');
        // }

        $user = $this->getUser($request);

        return view('2fa.otp', ['user' => $user]);
    }

    public function verify2faOtp(Request $request)
    {
        $user = $this->getUser($request);

        // if($user->two_factor_enabled) {
        //     return redirect()->route('home')->with('status', "2FA is already enabled.");
        // }

        $this->validate($request, [
            'otp' => [
                'required',
                'digits:6',
                function ($attribute, $value, $fail) use($request, $user) {
                    $google2fa = Container::getInstance()->make('pragmarx.google2fa');

                    $two_factor_secret = $request->session()->get('two_factor_secret', $user->two_factor_secret);

                    if (! $two_factor_secret || ! $google2fa->verifyKey($two_factor_secret, $value)) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },
            ]
        ]);

        if(! $user->two_factor_enabled) {
            $user->two_factor_enabled = true;
            $user->two_factor_secret = $request->session()->pull('two_factor_secret');
            $user->two_factor_recovery_codes = encrypt(json_encode(Collection::times(8, function () { 
                return $this->generateRecoveryCode(); 
            })->all()));
            // $user->two_factor_recovery_codes = encrypt(str_replace(
            //     $code,
            //     $this->generateRecoveryCode(),
            //     decrypt($this->two_factor_recovery_codes)
            // ));
            $user->save();
        }

        if(! $request->user()) {
            Auth::guard()->login($user, $request->session()->pull('remember'));

            $request->session()->forget('user');
            $request->session()->put('auth.otp_confirmed_at', time());
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath())->with('status', "Valid OTP.");
    }

    protected function getUser(Request $request)
    {
        $user = $request->user() ?: $request->session()->get('user');

        if(! $user) {
            throw new AuthenticationException('Unauthenticated.');
        }
        
        return $user;
    }

    protected function generateRecoveryCode()
    {
        return Str::random(10).'-'.Str::random(10);
    }
}
