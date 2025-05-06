<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Products/Index', [
            'products' => Product::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Product::create($validated);

        return Inertia::location(route('dashboard.products.index'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $product->update($validated);
        return Inertia::location(route('dashboard.products.index'));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return Inertia::location(route('dashboard.products.index'));
    }
}
