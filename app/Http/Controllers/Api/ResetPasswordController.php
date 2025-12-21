<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /**
     * Reset the user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Send email with the new password
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResetPassword($request->password));

        return response()->json([
            'message' => 'Berhasil reset password',
        ]);
    }
}
