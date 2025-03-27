<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LostItemController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'city' => 'required|string',
            'country' => 'required|string',
            'email' => 'required|email',
            'files.*' => 'nullable|file|mimes:jpg,png,pdf',
            'id_card_image' => 'nullable|string',
            'lost_date_time' => 'required|date|before:now',
            'lost_item_type' => 'required|in:Bag,Shoe,Watch,Other',
            'name' => 'required|string',
            'phone' => 'required|string',
            'purchase_location' => 'required|string',
            'serial_code' => 'required|string',
            'street_address' => 'required|string',
        ]);

        // Handle ID card image (Base64 or file)
        if ($request->hasFile('id_card_image')) {
            $validated['id_card_image'] = $request->file('id_card_image')->store('uploads', 'public');
        } elseif ($request->input('id_card_image')) {
            $validated['id_card_image'] = $this->storeBase64Image($request->input('id_card_image'));
        }

        if ($request->hasFile('files')) {
            // Store actual uploaded files
            $files = array_map(fn($file) => 'storage/' . $file->store('uploads', 'public'), $request->file('files'));
            $validated['files'] = json_encode($files);
        } elseif ($request->input('files')) {
            // Handle Base64 images
            $inputFiles = $request->input('files');

            // If it's an array of Base64 strings
            if (is_array($inputFiles)) {
                $files = array_map(fn($base64) => $this->storeBase64Image($base64), $inputFiles);
                $validated['files'] = json_encode($files);
            } else {
                // Handle a single Base64 string
                $file = $this->storeBase64Image($inputFiles);
                $validated['files'] = json_encode([$file]);
            }
        }


        LostItem::create($validated);

        return redirect()->back()->with('success', 'Lost item reported successfully.');
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
