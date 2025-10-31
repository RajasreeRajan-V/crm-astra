<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        
        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard.index');
        }

        return back()->with('error', 'Invalid login credentials');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

}
