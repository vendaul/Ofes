<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.'])->withInput();
        }

        $otp = (string) random_int(100000, 999999);
        $expireMinutes = (int) config('auth.passwords.users.expire', 10);
        $cacheKey = $this->otpCacheKey($request->email);

        Cache::put($cacheKey, [
            'otp_hash' => Hash::make($otp),
            'created_at' => now()->toDateTimeString(),
        ], now()->addMinutes($expireMinutes));

        try {
            Mail::raw(
                "Your password reset OTP is: {$otp}\n\nThis code will expire in {$expireMinutes} minutes.",
                function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Password Reset OTP');
                }
            );
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors([
                'email' => 'Unable to send OTP right now. Please try again later.',
            ])->withInput();
        }

        return redirect()
            ->route('password.reset', ['email' => $request->email])
            ->with('status', 'We sent an OTP code to your email. Enter it below to reset your password.');
    }

    protected function otpCacheKey(string $email): string
    {
        return 'password_reset_otp_' . sha1(strtolower(trim($email)));
    }
}
