<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Upload;
use App\Models\PartnerCompany;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Reports/Index');
    }

    public function lost()
    {
        $companies = PartnerCompany::all();
        $products = Product::all();
        return Inertia::render('Reports/Lost', [
            'companies' => $companies,
            'products' => $products,
        ]);
    }

    public function stolen()
    {
        $companies = PartnerCompany::all();
        $products = Product::all();
        return Inertia::render('Reports/Stolen', [
            'companies' => $companies,
            'products' => $products,
        ]);
    }

    public function submitted($tracking_code)
    {
        return Inertia::render('Reports/Submitted', [
            'tracking_code' => $tracking_code,
        ]);
    }

    public function tracking($tracking_code)
    {
        $report = Report::where('tracking_code', $tracking_code)->first();

        if (!$report) {
            abort(404);
        }

        return Inertia::render('Reports/TrackingPage', [
            'tracking_code' => $tracking_code,
            'report' => $report,
        ]);
    }

    public function liveTracking($trackingCode)
    {
        $report = Report::where('tracking_code', $trackingCode)->first();

        if (!$report) {
            abort(404);
        }

        return Inertia::render('Dashboard/Reports/LiveTracking', [
            'trackingCode' => $trackingCode,
            'report' => $report,
        ]);
    }
    public function status()
    {
        $status = [
            'received' => 'received',
            'checking_brand' => 'checking brand',
            'checking_company' => 'checking company',
            'transferred_to_police' => 'transferred to police',
            'done' => 'done',
        ];

        $currentUser = auth()->user();
        $reports = $currentUser->reports()->with(['company'])->get();

        return Inertia::render('Reports/Status', [
            'reports' => $reports,
            'statuses' => $status,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'date_time' => preg_replace('/\s*\(.*\)$/', '', $request->input('date_time')),
        ]);

        $data = $request->validate([
            'type' => 'required|in:stolen,lost',
            'customer_name' => 'required|string|max:255',
            'serial_code' => 'required|string',
            'item_type' => 'required|string|max:255',
            'date_time' => 'required|date|before:now',
            'country' => 'required|string',
            'city' => 'required|string',
            'street_address' => 'required|string',
            'company_id' => 'nullable|exists:partner_companies,id',
            'lost_ownership_document' => 'boolean',

            //  إضافة دعم للإحداثيات
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',

            'files.*' => 'nullable|file|mimes:jpg,png,pdf,webp',
            'id_card_image' => 'nullable|file|mimes:jpg,png,pdf,webp',
        ]);

        $data['tracking_code'] = uniqid('report_');
        $data['user_id'] = auth()->id();

        if (!$data['user_id']) {
            return redirect()->route('login');
        }

        $report = Report::create($data);

        if (!$report) {
            return response()->json(['message' => 'Failed to create report'], 500);
        }

        // صورة الهوية
        if ($request->hasFile('id_card_image')) {
            $path = $request->file('id_card_image')->store('uploads', 'public');
            Upload::create([
                'file_path' => 'storage/' . $path,
                'report_id' => $report->id,
                'file_type' => 'id_card'
            ]);
        }

        // الملفات الأخرى
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('uploads', 'public');
                Upload::create([
                    'report_id' => $report->id,
                    'file_path' => 'storage/' . $filePath,
                    'file_type' => 'product',
                ]);
            }
        }

        return Inertia::render('Reports/Submitted', [
            'tracking_code' => $report->tracking_code,
        ]);
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        return redirect()->route('reports.status')->with('success', 'Report deleted successfully');
    }

    public function exportPdf(Report $report)
    {
        $pdf = PDF::loadView('pdf.report', compact('report'));
        return $pdf->download("report-{$report->id}.pdf");
    }

    private function storeBase64Image($base64Image)
    {
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $decodedFile = base64_decode($imageData);
        if (!$decodedFile) return null;

        $fileName = 'uploads/' . uniqid() . '.png';
        Storage::disk('public')->put($fileName, $decodedFile);

        return 'storage/' . $fileName;
    }
}
