<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

    public function toogle2fa(Request $request)
    {
        // \Log::debug('toogle2fa#0', $request->all());

        $user = $request->user();

        // \Log::debug('toogle2fa#1', $user->toArray());

        if($request->has('google2fa_enabled')) {
            $user->google2fa_enabled = true;
            $user->google2fa_secret = Str::random(10);
            $user->save();

            // \Log::debug('toogle2fa#2', $user->toArray());

            return redirect()->back();
        }

        // show 2fa setup qrcode
        $user->google2fa_enabled = false;
        $user->google2fa_secret = '';
        $user->save();

        // \Log::debug('toogle2fa#3', $user->toArray());

        return redirect()->back();
    }
}
