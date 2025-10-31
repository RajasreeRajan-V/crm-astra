<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Agent;

class AgentAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.agent-login'); // Create this view for agent login
    }

    public function login(Request $request)
    {
        // $credentials = $request->only('phone_no', 'password');
         $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
            ]);
        $agent = Agent::where('email', $request->email)->first();

    if ($agent) {
        // If password is not bcrypt, convert it
        if (!password_get_info($agent->password)['algo']) {
            // Means password is plain text or not hashed → rehash now
            if ($agent->password === $request->password) {
                $agent->password = Hash::make($request->password);
                $agent->save();
            }
        }
    }

    // Now attempt login
    if (Auth::guard('agent')->attempt($request->only('email', 'password'))) {
        return redirect()->intended('/agent/dashboard');
    }
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout()
    {
        Auth::guard('agent')->logout();
        return redirect('/agent/login');
    }
}
