<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Upload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ReportController extends Controller
{
    public function store(Request $request)
    {

        $data = $request->validate([
            'city' => 'required|string',
            'country' => 'required|string',
            'date_time' => 'required|date|before:now',
            // TODO:
            'item_type' => 'required|in:Bag,Shoe,Watch,Other',
            'purchase_location' => 'required|string',
            'street_address' => 'required|string',
            'type' => 'required|in:stolen,lost',
            'product_name' => 'required|string|max:255',
            'serial_code' => 'required|string|unique:reports,serial_code',
            'location' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpg,png,pdf',
            'id_card_image' => 'nullable|string',

        ]);
        $data['user_id'] = auth()->user()->id;
        $report = Report::create($data);
        if (!$report) {
            return response()->json(['message' => 'Failed to create report'], 500);
        }

        if ($request->hasFile('files')) {
            $files = array_map(fn($file) => 'storage/' . $file->store('uploads', 'public'), $request->file('files'));
            foreach ($files as $file) {
                Upload::create([
                    'report_id' => $report->id,
                    'file_path' => $file,
                    'file_type' => 'product'
                ]);
            }
        } elseif ($request->input('files')) {
            $inputFiles = $request->input('files');
            if (is_array($inputFiles)) {
                $files = array_map(fn($base64) => $this->storeBase64Image($base64), $inputFiles);
                foreach ($files as $file) {
                    Upload::create([
                        'report_id' => $report->id,
                        'file_path' => $file,
                        'file_type' => 'product'
                    ]);
                }
            } else {
                $file = $this->storeBase64Image($inputFiles);
                Upload::create([
                    'report_id' => $report->id,
                    'file_path' => $file,
                    'file_type' => 'product'
                ]);
            }
        }

        if ($request->hasFile('id_card_image')) {
            $path = $request->file('id_card_image')->store('uploads', 'public');
            Upload::create([
                'report_id' => $report->id,
                'file_path' => 'storage/' . $path,
                'file_type' => 'id_card'
            ]);
        } elseif ($request->input('id_card_image')) {
            $imagePath = $this->storeBase64Image($request->input('id_card_image'));
            if ($imagePath) {
                Upload::create([
                    'report_id' => $report->id,
                    'file_path' => $imagePath,
                    'file_type' => 'id_card'
                ]);
            }
        }


        return redirect()->back()->with('success', 'Lost item reported successfully.');
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        return response()->json(['message' => 'Report deleted successfully']);
    }
    private function storeBase64Image($base64Image)
    {
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $decodedFile = base64_decode($imageData);

        if (!$decodedFile) {
            return null;
        }

        $fileName = 'uploads/' . uniqid() . '.png';
        Storage::disk('public')->put($fileName, $decodedFile);

        return 'storage/' . $fileName;
    }
}
