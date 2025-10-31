<?php

namespace App\Http\Controllers\Agent\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    // Redirect after successful password reset
    protected $redirectTo = '/agent/dashboard';

    protected function broker()
    {
        return Password::broker('agents');
    }

    protected function guard()
    {
        return Auth::guard('agent');
    }
}

