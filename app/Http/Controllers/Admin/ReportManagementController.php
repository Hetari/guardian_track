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
        $reports = Report::with(['user', 'company'])
            ->select(
                'id',
                'user_id',
                'type',
                'customer_name',
                'serial_code',
                'date_time',
                'country',
                'city',
                'street_address',
                'item_type',
                'status',
                'company_id',
                'lost_ownership_document',
                'tracking_code'
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
            'street_address' => 'required|string',
            'type' => 'required|in:stolen,lost',
            'customer_name' => 'required|string|max:255',
            'serial_code' => 'required|string',
            'status' => 'required|string',
            'company_id' => 'nullable|exists:partner_companies,id',
            'lost_ownership_document' => 'boolean',
            'tracking_code' => 'required|string|unique:reports,tracking_code,' . $request->id,
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
