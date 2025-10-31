<?php

namespace App\Http\Controllers\Agent\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    protected function broker()
    {
        // Use the agent's password broker
        return Password::broker('agents');
    }
}
