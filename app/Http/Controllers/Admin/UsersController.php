<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Users;

class UsersController extends Controller
{
    public function index()
    {
        $users = Users::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'Kullanıcı silindi.');
    }

    public function toggleStatus($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();
        return back()->with('success', 'Durum güncellendi.');
    }
}