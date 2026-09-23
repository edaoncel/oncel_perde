<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Mail\SendResetCodeMail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $code = rand(100000, 999999); 

        DB::table('password_reset_codes')->where('email', $request->email)->delete();
        
        DB::table('password_reset_codes')->insert([
            'email' => $request->email,
            'code' => $code,
            'created_at' => Carbon::now()
        ]);

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.gmail.com',
            'mail.mailers.smtp.port' => 587,
            'mail.mailers.smtp.encryption' => 'tls',
            'mail.mailers.smtp.username' => 'edaoncel15@gmail.com',
            'mail.mailers.smtp.password' => 'tbpjgmguwtzvxlxs',
            'mail.from.address' => 'edaoncel15@gmail.com',
            'mail.from.name' => config('app.name'),
        ]);

        try {
            Mail::to($request->email)->send(new SendResetCodeMail($code));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'MAIL HATASI: ' . $e->getMessage()
            ], 500);
        }
        
        return response()->json([
            'success' => true, 
            'message' => '6 haneli doğrulama kodu e-postanıza gönderildi.'
        ]);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric',
            'password' => 'required|min:6|confirmed',
        ]);

        $reset = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$reset) {
            return response()->json([
                'success' => false, 
                'message' => 'Geçersiz veya hatalı kod!'
            ], 422);
        }

        if (Carbon::parse($reset->created_at)->addMinutes(15)->isPast()) {
            return response()->json([
                'success' => false, 
                'message' => 'Kodun süresi dolmuş. Lütfen tekrar kod isteyin.'
            ], 422);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Şifreniz başarıyla güncellendi. Giriş yapabilirsiniz.'
        ]);
    }
}