<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    /* ================= REGISTER (SEND OTP) ================= */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|digits:10|unique:users',
            'password' => 'required|min:6',
        ]);

        $otp = rand(100000, 999999);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
            'is_verified' => false,
        ]);

        // ⚠️ SMS later – for now return OTP
        return response()->json([
            'message' => 'OTP sent to mobile',
            'otp_debug' => $otp
        ]);
    }

    /* ================= VERIFY OTP ================= */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || $user->otp !== $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'is_verified' => true,
        ]);

        return response()->json(['message' => 'Admin verified']);
    }

    /* ================= LOGIN ================= */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->is_verified) {
            return response()->json(['message' => 'Account not verified'], 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('admin')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    /* ================= FORGOT PASSWORD (SEND OTP) ================= */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users,phone',
        ]);

        $otp = rand(100000, 999999);

        $user = User::where('phone', $request->phone)->first();
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        return response()->json([
            'message' => 'OTP sent for password reset',
            'otp_debug' => $otp
        ]);
    }

    /* ================= RESET PASSWORD ================= */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || $user->otp !== $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json(['message' => 'Password reset successful']);
    }
}
