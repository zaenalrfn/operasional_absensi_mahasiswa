<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'photo' => ['nullable', 'image', 'max:2048'], // 2MB Max
            // Adding student specific fields that might be updateable
            'program_studi' => ['nullable', 'string', 'max:255'],
            'semester' => ['nullable', 'integer'],
            'kelas' => ['nullable', 'string', 'max:255'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('program_studi')) {
            $user->program_studi = $request->program_studi;
        }

        if ($request->filled('semester')) {
            $user->semester = $request->semester;
        }

        if ($request->filled('kelas')) {
            $user->kelas = $request->kelas;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
                Storage::disk('public')->delete($user->photo_url);
            }

            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->photo_url = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }
}
