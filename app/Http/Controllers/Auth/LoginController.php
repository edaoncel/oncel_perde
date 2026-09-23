<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        return view('auth.userlogin');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => ['Girdiğiniz bilgiler kayıtlarımızla uyuşmuyor.'],
        ]);
    }
}
    