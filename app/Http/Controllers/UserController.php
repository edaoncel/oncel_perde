<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function dashboard()
    {
       
        return view('dashboard.orders'); 
    }

    public function profile() 
    {
        return view('dashboard.profile');
    }

    public function cart() 
    {
        $cart = \App\Models\Cart::with('product')->where('user_id', Auth::id())->get();
        
        return view('dashboard.cart', compact('cart')); 
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $user->update($request->only('name', 'email'));

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        return back()->with('success', 'Profiliniz başarıyla güncellendi.');
    }

    public function showLoginForm() 
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('users.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'is_active' => true])) {
            $request->session()->regenerate();
           return redirect()->to('/dashboard/favorilerim');
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user && !$user->is_active) {
            return back()->withErrors(['email' => 'Hesabınız pasif durumdadır. Lütfen yönetici ile iletişime geçin.']);
        }

        return back()->withErrors(['email' => 'Giriş bilgileriniz hatalı. Kayıt olmadıysanız lütfen kaydolun.']);
    }

    public function register(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        return redirect()->route('dashboard');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}