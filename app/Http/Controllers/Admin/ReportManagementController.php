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
            ->latest('created_at')
            ->get();

        return Inertia::render('Dashboard/Reports/Index', [
            'reports' => $reports,
        ]);
    }

    public function edit(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:reports,id',
            'status' => 'required|string',
            // 'customer_name' => 'required|string|max:255',
            // 'serial_code' => 'required|string|max:255',
            // 'date_time' => 'required|date|before:now',
            // 'country' => 'required|string|max:255',
            // 'city' => 'required|string|max:255',
            // 'street_address' => 'required|string|max:255',
            // 'item_type' => 'required|in:Bag,Shoe,Watch,Other',
            // 'type' => 'required|in:stolen,lost',
            // 'company_id' => 'nullable|exists:partner_companies,id',
            // 'lost_ownership_document' => 'required|boolean',
            // 'tracking_code' => 'required|string|max:255|unique:reports,tracking_code,' . $request->id,
            // 'files.*' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            // 'id_card_image' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $report = Report::findOrFail($validated['id']);
        $report->update($validated);

        return Inertia::location(route('dashboard.reports.index'));
    }

    public function delete($id)
    {
        $report = Report::find($id);
        if (!$report) {
            return back()->withErrors(['report' => 'Report not found']);
        }

        $report->delete();

        return Inertia::location(route('dashboard.reports.index'));
    }
}
