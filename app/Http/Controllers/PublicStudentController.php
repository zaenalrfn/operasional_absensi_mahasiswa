<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class PublicStudentController extends Controller
{
    public function create()
    {
        $jurusans = \App\Models\Course::select('jurusan')
            ->distinct()
            ->whereNotNull('jurusan')
            ->orderBy('jurusan')
            ->pluck('jurusan');

        return Inertia::render('Guest/StudentRegister', [
            'jurusans' => $jurusans
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'program_studi' => 'required|string',
            'photo' => 'required|image|max:2048', // 2MB Max
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profile-photos', 'public');
        }

        // --- Automatic NIM Generation ---
        $prefix = '5';
        $yearSuffix = date('y'); // 23 for 2023

        // Map Program Jurusan to Codes (MM)
        // Hardcoded based on user info + assumptions
        $jurusanCodes = [
            'Teknik Informatika' => '04',
            'Sistem Informasi' => '05',
            'Sains Data' => '18',
            'Bisnis Digital' => '07', // Example from seeder (203507-23)
            // Add defaults for others
        ];

        $jurusanCode = $jurusanCodes[$request->program_studi] ?? '99'; // Default '99' if not found
        $staticPart = '11';

        $nimBase = $prefix . $yearSuffix . $jurusanCode . $staticPart;

        // Find next sequence number (NNN)
        // We look for the latest NIM starting with this base
        $latestUser = User::where('nim', 'LIKE', $nimBase . '%')
            ->orderBy('nim', 'desc')
            ->first();

        $sequence = 1;
        if ($latestUser) {
            $lastNim = $latestUser->nim;
            $lastSequence = intval(substr($lastNim, -3));
            $sequence = $lastSequence + 1;
        }

        $nim = $nimBase . str_pad($sequence, 3, '0', STR_PAD_LEFT);
        // -------------------------------

        // Automatic Class Assignment Logic
        $semester = 1; // Default
        $maxCapacity = 40;
        $classes = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $assignedClass = null;

        foreach ($classes as $class) {
            $count = User::where('program_studi', $request->program_studi)
                ->where('semester', $semester)
                ->where('kelas', $class)
                ->count();

            if ($count < $maxCapacity) {
                $assignedClass = $class;
                break;
            }
        }

        if (!$assignedClass) {
            return back()->withErrors(['program_studi' => 'Mohon maaf, semua kelas untuk jurusan ini di semester 1 sudah penuh (A-G).']);
        }

        $user = User::create([
            'nim' => $nim,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'program_studi' => $request->program_studi,
            'semester' => $semester,
            'kelas' => $assignedClass,
            'photo_url' => $photoPath,
        ]);

        $user->assignRole('Mahasiswa');

        return redirect()->route('login')->with('status', "Registrasi Mahasiswa berhasil! Anda masuk ke Kelas $assignedClass. Silakan login.");
    }
}
