<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * Get list of app versions for "What's New" history.
     */
    public function index()
    {
        $versions = AppVersion::orderBy('release_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'message' => 'App version history retrieved successfully',
            'data' => $versions,
            'latest_version' => $versions->first(),
        ]);
    }

    /**
     * Check for latest update.
     */
    public function checkUpdate(Request $request)
    {
        $platform = $request->query('platform'); // 'android' or 'ios'

        $query = AppVersion::orderBy('release_date', 'desc')
            ->orderBy('id', 'desc');

        if ($platform) {
            $query->where(function ($q) use ($platform) {
                $q->where('platform', $platform)
                    ->orWhereNull('platform');
            });
        }

        $latest = $query->first();

        return response()->json([
            'data' => $latest,
        ]);
    }
}
