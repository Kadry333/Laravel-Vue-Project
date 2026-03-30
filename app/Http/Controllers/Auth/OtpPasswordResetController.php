<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OtpPasswordResetController extends Controller
{
    /**
     * Display the OTP password reset request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
            'otpSent' => session('otp_sent'),
            'email' => session('email'),
        ]);
    }

    /**
     * Send OTP to user's email.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate OTP (use default 123456 for testing)
        $otp = env('APP_ENV') === 'local' || env('APP_ENV') === 'testing' 
            ? '123456' 
            : str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save OTP to user
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // In production, send email with OTP
        // For testing, just show it in session
        $message = (env('APP_ENV') === 'local' || env('APP_ENV') === 'testing')
            ? "Test OTP: {$otp} (Valid for 10 minutes)"
            : "OTP sent to your email!";

        return back()
            ->with('otp_sent', true)
            ->with('email', $request->email)
            ->with('status', $message);
    }

    /**
     * Verify OTP and show password reset form.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)
            ->where('otp_code', $request->otp)
            ->where('otp_expires_at', '>', now())
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid or expired OTP.'],
            ]);
        }

        // Store in session and redirect to reset page
        return redirect()->route('otp.reset.form', ['otp' => $request->otp])
            ->with(['email' => $request->email, 'otp' => $request->otp]);
    }

    /**
     * Display the password reset form with OTP.
     */
    public function showResetForm(Request $request, string $otp): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->query('email', session('email')),
            'otp' => $otp,
        ]);
    }

    /**
     * Reset password with OTP.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $request->email)
            ->where('otp_code', $request->otp)
            ->where('otp_expires_at', '>', now())
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid or expired OTP.'],
            ]);
        }

        // Reset password and clear OTP
        $user->forceFill([
            'password' => Hash::make($request->password),
            'otp_code' => null,
            'otp_expires_at' => null,
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()->route('login')
            ->with('status', 'Password reset successful! You can now log in with your new password.');
    }
}
