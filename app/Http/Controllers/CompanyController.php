<?php

namespace App\Http\Controllers;

use App\Models\PartnerCompany as Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(): Response
    {
        $companies = Company::all();
        return Inertia::render('Dashboard/Companies/Index', [
            'companies' => $companies
        ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:partner_companies,email',
            'active' => 'required|boolean',
        ]);

        Company::create($validated);

        return Inertia::location(route('dashboard.companies.index'));
    }

    public function edit(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:partner_companies,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:partner_companies,email,' . $request->id,
            'active' => 'required|boolean',
        ]);

        $company = Company::findOrFail($validated['id']);
        $company->update($validated);


        return Inertia::location(route('dashboard.companies.index'));
    }

    public function delete($id)
    {
        Company::findOrFail($id)->delete();

        return Inertia::location(route('dashboard.companies.index'));
    }
}
