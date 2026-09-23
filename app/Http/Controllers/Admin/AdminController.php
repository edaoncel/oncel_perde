<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
public function index()
{
    $admins = Admin::all();
    return view('admin.admins', compact('admins'));
}

public function admins()
{
    return $this->index();
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Ad Soyad alanı zorunludur.',
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kullanımda.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
            'password.confirmed' => 'Şifreler birbiriyle uyuşmuyor.',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admins.index')->with('success', 'Yeni yönetici başarıyla eklendi.');
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Yeni şifre alanı zorunludur.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
            'password.confirmed' => 'Şifreler birbiriyle uyuşmuyor.',
        ]);

        $admin = Admin::findOrFail($id);
        $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect()->route('admins.index')->with('success', 'Yöneticinin şifresi başarıyla güncellendi.');
    }

    public function destroy($id)
    {
        if (Auth::guard('admin')->id() == $id) {
            return redirect()->route('admins.index')->with('error', 'Kendi yönetici hesabınızı silemezsiniz!');
        }

        $admin = Admin::findOrFail($id);
        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'Yönetici sistemden başarıyla kaldırıldı.');
    }
}