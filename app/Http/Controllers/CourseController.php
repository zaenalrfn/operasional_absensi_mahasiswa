<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lectures;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('lecturer')->latest()->get();
        return Inertia::render('Courses/Index', ['courses' => $courses]);
    }

    public function create()
    {
        $lecturers = Lectures::all();
        return Inertia::render('Courses/Create', ['lecturers' => $lecturers]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mk' => 'required|string|unique:courses,kode_mk',
            'nama_mk' => 'required|string',
            'jurusan' => 'required|string',
            'dosen_id' => 'required|exists:lectures,id',
            'sks' => 'required|integer',
            'kelas' => 'required|string',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'semester' => 'required|integer',
        ]);

        Course::create($request->all());

        return redirect()->route('courses.index')->with('success', 'Mata Kuliah berhasil ditambahkan.');
    }

    public function edit(Course $course)
    {
        $lecturers = Lectures::all();
        return Inertia::render('Courses/Edit', ['course' => $course, 'lecturers' => $lecturers]);
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'kode_mk' => 'required|string|unique:courses,kode_mk,' . $course->id,
            'nama_mk' => 'required|string',
            'jurusan' => 'required|string',
            'dosen_id' => 'required|exists:lectures,id',
            'sks' => 'required|integer',
            'kelas' => 'required|string',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'semester' => 'required|integer',
        ]);

        $course->update($request->all());

        return redirect()->route('courses.index')->with('success', 'Mata Kuliah berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Mata Kuliah berhasil dihapus.');
    }
}
