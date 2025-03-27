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
            'id_card_image' => 'nullable|file|mimes:jpg,png,pdf',
            'lost_date_time' => 'required|date|before:now',
            'lost_item_type' => 'required|in:Bag,Shoe,Watch,Other',
            'name' => 'required|string',
            'phone' => 'required|string',
            'purchase_location' => 'required|string',
            'serial_code' => 'required|string',
            'street_address' => 'required|string',
        ]);

        dd($validated);

        if ($request->hasFile('id_card_image')) {
            $validated['id_card_image'] = $request->file('id_card_image')->store('uploads', 'public');
        }

        if ($request->hasFile('files')) {
            $files = [];
            foreach ($request->file('files') as $file) {
                $files[] = $file->store('uploads', 'public');
            }
            $validated['files'] = json_encode($files);
        }

        LostItem::create($validated);

        return redirect()->back()->with('success', 'Lost item reported successfully.');
    }
}
