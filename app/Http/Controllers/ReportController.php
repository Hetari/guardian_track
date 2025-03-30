<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Upload;
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
            'serial_code' => 'required|string',
            'files.*' => 'nullable|file|mimes:jpg,png,pdf',
            // 'id_card_image' => 'nullable|file|mimes:jpg,png,pdf',
            'id_card_image' => 'nullable|file|mimes:jpg,png,pdf',
        ]);

        $data['user_id'] = auth()->check() ? auth()->user()->id : null;
        if (!$data['user_id']) {
            return redirect()->route('login');
        }

        $report = Report::create($data);
        if (!$report) {
            return response()->json(['message' => 'Failed to create report'], 500);
        }


        // dd($request->hasFile('files'));
        if ($request->hasFile('id_card_image')) {
            $data['id_card_image'] = $request->file('id_card_image')->store('uploads', 'public');
            Upload::create([
                'file_path' => $data['id_card_image'],
                'report_id' => $report->id,
                'file_type' => 'id_card'
            ]);
        }

        if ($request->hasFile('files')) {
            // Get array of uploaded files
            $uploadedFiles = $request->file('files');

            foreach ($uploadedFiles as $file) {
                // Store each file on the public disk (returns the file path relative to storage/app/public)
                $filePath = 'storage/' . $file->store('uploads', 'public');

                // Create an upload record for each file
                Upload::create([
                    'report_id' => $report->id,
                    'file_path' => $filePath,
                    'file_type' => 'product', // adjust this value as needed
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
        // Remove the data URL prefix (if present)
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $decodedFile = base64_decode($imageData);

        if (!$decodedFile) {
            return null;
        }

        // Generate a unique filename
        $fileName = 'uploads/' . uniqid() . '.png';

        // Store the decoded image
        Storage::disk('public')->put($fileName, $decodedFile);

        return 'storage/' . $fileName; // Fixed: Return accessible path
    }
}
