<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLocation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceLocationController extends Controller
{
    /**
     * Display a listing of the resource (Web).
     */
    public function index()
    {
        $locations = AttendanceLocation::orderBy('created_at', 'desc')->get();
        return Inertia::render('AttendanceLocations/Index', [
            'locations' => $locations,
        ]);
    }

    /**
     * Display a listing of the resource (API).
     */
    public function apiIndex()
    {
        $locations = AttendanceLocation::all();
        return response()->json($locations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:1',
        ]);

        AttendanceLocation::create($request->all());

        return redirect()->back()->with('success', 'Lokasi berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $location = AttendanceLocation::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:1',
        ]);

        $location->update($request->all());

        return redirect()->back()->with('success', 'Lokasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $location = AttendanceLocation::findOrFail($id);
        $location->delete();

        return redirect()->back()->with('success', 'Lokasi berhasil dihapus.');
    }
}
