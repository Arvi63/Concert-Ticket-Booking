<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        if (!Auth::attempt(request(['email', 'password']))) {
            return redirect('/login')->withErrors([
                'email' => ['These Credentials do not match our records'],
            ]);
        }

        return redirect('/backstage/concerts');
    }
}
