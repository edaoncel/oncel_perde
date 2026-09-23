<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token', '_method') as $key => $value) {
            if ($request->hasFile($key) && $request->file($key)->isValid()) {
                $file = $request->file($key);
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/settings'), $fileName);
                $value = 'uploads/settings/' . $fileName;

                $oldSetting = Setting::where('key', $key)->first();
                if ($oldSetting && $oldSetting->value && File::exists(public_path($oldSetting->value))) {
                    File::delete(public_path($oldSetting->value));
                }
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Ayarlar başarıyla güncellendi.');
    }
}