<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SerialNumber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SerialNumberController extends Controller
{
    public function index()
    {
        $serials = SerialNumber::with(['user', 'product'])->latest()->get();
        $users = User::all();
        $products = Product::all();

        return Inertia::render('Dashboard/SerialNumbers/Index', [
            'serials' => $serials,
            'users' => $users,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $data['serial_code'] = strtoupper(Str::uuid());

        SerialNumber::create($data);

        return Inertia::location(route('dashboard.serial-numbers.index'));
    }

    public function update(Request $request, SerialNumber $serialNumber)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $serialNumber->update([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            // optional: don't regenerate SN
        ]);

        return Inertia::location(route('dashboard.serial-numbers.index'));
    }

    public function destroy(SerialNumber $serialNumber)
    {
        $serialNumber->delete();
        return Inertia::location(route('dashboard.serial-numbers.index'));
    }
}
