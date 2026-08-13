<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'data' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    /**
     * Update profile.
     * - If email unchanged: update name only, no OTP needed.
     * - If email changed: send OTP to current email, return requires_verification.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        // Always update name immediately
        $user->name = $data['name'];
        $user->save();

        // If email is unchanged, we're done
        if ($data['email'] === $user->email) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
            ]);
        }

        // Email is changing — generate OTP, send to CURRENT email
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP and pending new email in cache for 15 minutes
        $cacheKey = 'email_change_otp_' . $user->id;
        Cache::put($cacheKey, [
            'otp'       => Hash::make($otp),
            'new_email' => $data['email'],
        ], now()->addMinutes(15));

        // Send OTP to the CURRENT email
        Mail::send('emails.email_change_otp', [
            'otp'       => $otp,
            'name'      => $user->name,
            'new_email' => $data['email'],
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Verify Your Email Change — Soft Cactus');
        });

        return response()->json([
            'success'               => true,
            'requires_verification' => true,
            'message'               => 'A 6-digit verification code has been sent to your current email address.',
        ]);
    }

    /**
     * Verify the OTP and apply the new email.
     */
    public function verifyEmailChange(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $cacheKey = 'email_change_otp_' . $user->id;
        $cached   = Cache::get($cacheKey);

        if (!$cached) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please try again.',
            ], 400);
        }

        if (!Hash::check($request->otp, $cached['otp'])) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect verification code. Please try again.',
            ], 400);
        }

        // Apply the new email
        $user->email = $cached['new_email'];
        $user->save();

        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Email updated successfully.',
            'data' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    /**
     * Change password — requires current password verification.
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:8|max:128',
            'password_confirmation' => 'required|same:password',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Revoke all other tokens for security
        $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }
}
