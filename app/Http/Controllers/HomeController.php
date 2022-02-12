<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     * 
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // dd($request->user()->toArray());

        return view('home');
    }

    public function show2faQrcode(Request $request)
    {
        $user = $request->user();

        if(! $user->google2fa_enabled) {
            return redirect()->route('home')->with('status', "2FA is not enabled.");
        }

        $google2fa = Container::getInstance()->make('pragmarx.google2fa');

        $qrcode_svg = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->google2fa_secret, // $google2fa->generateSecretKey()
        );

        return view('2fa.qrcode', ['qrcode_svg' => $qrcode_svg]);
    }

    public function show2faOtp(Request $request)
    {
        if(! $request->user()->google2fa_enabled) {
            return redirect()->route('home')->with('status', "2FA is not enabled.");
        }

        return view('2fa.otp');
    }

    public function verify2faOtp(Request $request)
    {
        $user = $request->user();

        if(! $request->user()->google2fa_enabled) {
            return redirect()->route('home')->with('status', "2FA is not enabled.");
        }

        $this->validate($request, [
            'otp' => [
                'required',
                'digits:6',
                function ($attribute, $value, $fail) use($user) {
                    $google2fa = Container::getInstance()->make('pragmarx.google2fa');

                    if (! $google2fa->verifyKey($user->google2fa_secret, $value)) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },
            ]
        ]);

        return redirect()->route('home')->with('status', "2FA setup successfully.");
    }

    public function toogle2fa(Request $request)
    {
        // \Log::debug('toogle2fa#0', $request->all());

        $user = $request->user();

        // \Log::debug('toogle2fa#1', $user->toArray());

        if($request->has('google2fa_enabled')) {
            $google2fa = Container::getInstance()->make('pragmarx.google2fa');

            $user->google2fa_enabled = true;
            $user->google2fa_secret = $google2fa->generateSecretKey(); // '';
            $user->save();

            // \Log::debug('toogle2fa#2', $user->toArray());

            return redirect()->route('2fa.qrcode');
        }

        // show 2fa setup qrcode
        $user->google2fa_enabled = false;
        $user->google2fa_secret = '';
        $user->save();

        // \Log::debug('toogle2fa#3', $user->toArray());

        return redirect()->back();
    }
}
