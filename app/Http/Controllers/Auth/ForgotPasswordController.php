<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Send password reset link — generates a token and emails a link
     * pointing to the React backoffice frontend, not the Laravel backend.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Always return success to prevent user enumeration attacks
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Delete any existing reset tokens for this email
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            // Generate a secure token
            $token = Str::random(64);

            // Store hashed token
            DB::table('password_reset_tokens')->insert([
                'email'      => $request->email,
                'token'      => Hash::make($token),
                'created_at' => now(),
            ]);

            // Build the reset URL pointing to the React backoffice
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            $resetUrl = $frontendUrl . '/reset-password'
                . '?token=' . urlencode($token)
                . '&email=' . urlencode($request->email);

            // Send the email
            Mail::send('emails.password_reset', [
                'resetUrl' => $resetUrl,
                'name'     => $user->name,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Reset Your Password — Soft Cactus Backoffice');
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'If that email address is in our system, you will receive a password reset link shortly.',
        ]);
    }
}
