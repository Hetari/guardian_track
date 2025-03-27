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
            // TODO: fix this validation rule
            'idCardImage' => 'nullable|file|mimes:jpg,png,pdf',
            'lostDateTime' => 'required|date|before:now',
            'lostItemType' => 'required|in:Bag,Shoe,Watch,Other',
            'name' => 'required|string',
            'phone' => 'required|string',
            'purchaseLocation' => 'required|string',
            'serialCode' => 'required|string',
            'streetAddress' => 'required|string',
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
