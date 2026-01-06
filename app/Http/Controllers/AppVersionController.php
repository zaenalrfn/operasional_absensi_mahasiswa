<?php

namespace App\Http\Controllers;

use App\Models\AppVersion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppVersionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $versions = AppVersion::orderBy('release_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('AppVersions/Index', [
            'versions' => $versions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('AppVersions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'version_number' => 'required|string|max:255',
            'release_date' => 'required|date',
            'release_notes' => 'nullable|array', // Expecting array of strings
            'release_notes.*' => 'string',
            'is_mandatory' => 'boolean',
            'platform' => 'nullable|string',
            'download_url' => 'nullable|url',
        ]);

        AppVersion::create($request->all());

        return redirect()->route('app-versions.index')
            ->with('message', 'App version created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppVersion $appVersion)
    {
        return Inertia::render('AppVersions/Edit', [
            'appVersion' => $appVersion
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AppVersion $appVersion)
    {
        $request->validate([
            'version_number' => 'required|string|max:255',
            'release_date' => 'required|date',
            'release_notes' => 'nullable|array',
            'release_notes.*' => 'string',
            'is_mandatory' => 'boolean',
            'platform' => 'nullable|string',
            'download_url' => 'nullable|url',
        ]);

        $appVersion->update($request->all());

        return redirect()->route('app-versions.index')
            ->with('message', 'App version updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppVersion $appVersion)
    {
        $appVersion->delete();

        return redirect()->route('app-versions.index')
            ->with('message', 'App version deleted successfully.');
    }
}
