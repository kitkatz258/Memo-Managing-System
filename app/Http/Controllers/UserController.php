<?php

namespace App\Http\Controllers;

use Dom\Attr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(){
        return view('login');
    }

    public function logincheck(Request $request){
        $credential = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if(Auth::attempt($credential)){
            return redirect()->route('dashboard');
        }
    }

    public function dashboard(){
        if(Auth::check() && Auth::user()->usertype == 'admin'){
            return view('admin.dashboard');
        }
        else if(Auth::check() && Auth::user()->usertype == 'user'){
            return view('user.home');
        }
    }
}
