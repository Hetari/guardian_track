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
            'name' => 'required|string',
            'email' => 'required|email',
            'serialCode' => 'required|string',
            'lostDateTime' => 'required|date',
            'phone' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            'streetAddress' => 'required|string',
            'idCardImage' => 'nullable|file|mimes:jpg,png,pdf',
            'purchaseLocation' => 'required|string',
            'files.*' => 'nullable|file|mimes:jpg,png,pdf',
            'lostItemType' => 'required|in:Bag,Shoe,Watch,Other',
        ]);

        if ($request->hasFile('idCardImage')) {
            $validated['idCardImage'] = $request->file('idCardImage')->store('uploads', 'public');
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
