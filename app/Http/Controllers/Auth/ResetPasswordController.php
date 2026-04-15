<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request)
    {
        return view('auth.passwords.reset', [
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $cacheKey = $this->otpCacheKey($request->email);
        $otpData = Cache::get($cacheKey);

        if (!$otpData || !isset($otpData['otp_hash']) || !Hash::check($request->otp, $otpData['otp_hash'])) {
            return back()->withErrors([
                'otp' => 'The OTP is invalid or has expired.',
            ])->withInput($request->only('email'));
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'We could not find a user with that email address.',
            ])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->remember_token = Str::random(60);
        $user->password_reset_at = now();
        $user->save();

        Cache::forget($cacheKey);

        return redirect()
            ->route('choose.role')
            ->with('status', 'Your password has been reset successfully. You can now log in.');
    }

    protected function otpCacheKey(string $email): string
    {
        return 'password_reset_otp_' . sha1(strtolower(trim($email)));
    }
}
