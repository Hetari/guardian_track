<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportManagementController extends Controller
{
    public function index()
    {
        $reports = Report::with('user')
            ->select(
                'id',
                'user_id',
                'type',
                'product_name',
                'serial_code',
                'date_time',
                'country',
                'city',
                'street_address',
                'purchase_location',
                'item_type',
                'status'
            )
            ->get();

        return Inertia::render('Dashboard/Reports', [
            'reports' => $reports,
        ]);
    }

    public function edit(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:reports,id',
            'city' => 'required|string',
            'country' => 'required|string',
            'date_time' => 'required|date|before:now',
            'item_type' => 'required|in:Bag,Shoe,Watch,Other',
            'purchase_location' => 'required|string',
            'street_address' => 'required|string',
            'type' => 'required|in:stolen,lost',
            'product_name' => 'required|string|max:255',
            'serial_code' => 'required|string',
            'status' => 'required|string',
            'files.*' => 'nullable|file|mimes:jpg,png,pdf',
            'id_card_image' => 'nullable|file|mimes:jpg,png,pdf',
        ]);

        $report = Report::find($validated['id']);
        if (!$report) {
            return back()->withErrors(['report' => 'Report not found']);
        }

        $report->update($validated);

        return Inertia::location(route('reports'));
    }

    public function delete($id)
    {
        $report = Report::find($id);
        if (!$report) {
            return back()->withErrors(['report' => 'Report not found']);
        }

        $report->delete();

        return Inertia::location(route('reports'));
    }
}
