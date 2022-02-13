<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Container\Container;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function index(Request $request)
    {
        return view('home');
    }

    public function toogle2fa(Request $request)
    {
        $user = $request->user();

        if($request->has('google2fa_enabled')) {
            return redirect()->route('2fa.qrcode');
        }

        $user->google2fa_enabled = false;
        $user->google2fa_secret = '';
        $user->save();

        return redirect()->back();
    }

    public function show2faQrcode(Request $request)
    {
        $user = $this->getUser($request);

        if($user->google2fa_enabled) {
            // return redirect()->route('home')->with('status', "2FA is already enabled.");
            return redirect()->route('2fa.otp')->with('status', "2FA is already enabled.");
        }

        $google2fa = Container::getInstance()->make('pragmarx.google2fa');

        $google2fa_secret = $google2fa->generateSecretKey();

        $request->session()->put('google2fa_secret', $google2fa_secret);

        $qrcode_svg = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $google2fa_secret,
        );

        return view('2fa.qrcode', [
            'qrcode_svg' => $qrcode_svg, 
            'google2fa_secret' => $google2fa_secret,
        ]);
    }

    public function show2faOtp(Request $request)
    {
        // if($request->user()->google2fa_enabled) {
        //     return redirect()->route('home')->with('status', "2FA is already enabled.");
        // }

        // if(! $request->user()->google2fa_enabled) {
        //     return redirect()->route('2fa.qrcode');
        // }

        $user = $this->getUser($request);

        return view('2fa.otp', ['user' => $user]);
    }

    public function verify2faOtp(Request $request)
    {
        $user = $this->getUser($request);

        // if($user->google2fa_enabled) {
        //     return redirect()->route('home')->with('status', "2FA is already enabled.");
        // }

        $this->validate($request, [
            'otp' => [
                'required',
                'digits:6',
                function ($attribute, $value, $fail) use($request, $user) {
                    $google2fa = Container::getInstance()->make('pragmarx.google2fa');

                    $google2fa_secret = $request->session()->get('google2fa_secret', $user->google2fa_secret);

                    if (! $google2fa_secret || ! $google2fa->verifyKey($google2fa_secret, $value)) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },
            ]
        ]);

        if(! $user->google2fa_enabled) {
            $user->google2fa_enabled = true;
            $user->google2fa_secret = $request->session()->pull('google2fa_secret');
            $user->save();
        }

        if(! $request->user()) {
            Auth::guard()->login($user, $request->session()->pull('remember'));

            $request->session()->forget(['user', 'remember']);
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
}
