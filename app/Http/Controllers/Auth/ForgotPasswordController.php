<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /**
     * Step 1: Validate email exists, generate OTP, send it.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Check if user exists — return error if not
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email address.',
            ], 404);
        }

        // Delete any existing OTP for this email
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store hashed OTP
        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($otp),
            'created_at' => now(),
        ]);

        // Send OTP email
        Mail::send('emails.otp_reset', [
            'otp'  => $otp,
            'name' => $user->name,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Your Password Reset Code — Soft Cactus');
        });

        return response()->json([
            'success' => true,
            'message' => 'A 6-digit verification code has been sent to your email.',
        ]);
    }

    /**
     * Step 2: Verify the OTP is correct and not expired.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'otp'   => 'required|string|size:6',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code.',
            ], 400);
        }

        // OTP expires after 15 minutes
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.',
            ], 400);
        }

        if (!Hash::check($request->otp, $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect verification code. Please try again.',
            ], 400);
        }

        // OTP is valid — return it as the token for the reset step
        return response()->json([
            'success' => true,
            'message' => 'Code verified successfully.',
            'token'   => $request->otp,
            'email'   => $request->email,
        ]);
    }

    /**
     * Legacy: kept for backwards compat — redirects to sendOtp
     */
    public function sendResetLink(Request $request)
    {
        return $this->sendOtp($request);
    }
}
