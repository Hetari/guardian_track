<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\Tracking;

class TrackingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tracking_code' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Check if a record with the same tracking code exists
        $tracking = Tracking::where('tracking_code', $validated['tracking_code'])->first();
        $report = Report::where('tracking_code', $tracking->tracking_code)->first();

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        if ($tracking) {
            $report->update([
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);
            $tracking->update([
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);
        } else {
            if ($report) {
                if ($report->latitude === null || $report->longitude === null) {
                    Tracking::create([
                        'user_id' => $validated['user_id'],
                        'tracking_code' => $validated['tracking_code'],
                        'latitude' => null,
                        'longitude' => null,
                    ]);
                } else {
                    Tracking::create($validated);
                }
            }
        }

        return response()->json(['message' => 'Tracking data processed successfully.'], 200);
    }

    public function latestLocation($trackingCode)
    {
        $tracking = Tracking::where('tracking_code', $trackingCode)
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$tracking) {
            return response()->json(['message' => 'No tracking data found'], 404);
        }
        if ($tracking->latitude === null || $tracking->longitude === null) {
            return response()->json(['message' => 'Location data not available'], 404);
        }

        return response()->json([
            'latitude' => $tracking->latitude,
            'longitude' => $tracking->longitude,
            'updated_at' => $tracking->updated_at,
        ]);
    }
}
