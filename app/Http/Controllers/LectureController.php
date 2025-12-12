<?php

namespace App\Http\Controllers;

use App\Models\Lectures;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LectureController extends Controller
{
    public function index()
    {
        $lectures = Lectures::latest()->get();
        return Inertia::render('Lectures/Index', ['lectures' => $lectures]);
    }

    public function create()
    {
        return Inertia::render('Lectures/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:lectures,email',
        ]);

        Lectures::create($request->all());

        return redirect()->route('lectures.index')->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function edit(Lectures $lecture)
    {
        return Inertia::render('Lectures/Edit', ['lecture' => $lecture]);
    }

    public function update(Request $request, Lectures $lecture)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:lectures,email,' . $lecture->id,
        ]);

        $lecture->update($request->all());

        return redirect()->route('lectures.index')->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function destroy(Lectures $lecture)
    {
        $lecture->delete();
        return redirect()->route('lectures.index')->with('success', 'Dosen berhasil dihapus.');
    }
}
