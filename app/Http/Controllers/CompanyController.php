<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCompanyRequest;
use App\Http\Resources\CompanyCollection;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Company::query();

        if ($request->has('search') && $request->search !== '') {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . $request->search . '%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $request->search . '%']);
        }

        $companies = $query->orderBy('name', 'asc')->paginate($request->get('per_page', 10));

        return Inertia::render('Companies/Index', [
            'companies' => new CompanyCollection($companies)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Companies/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCompanyRequest $request)
    {
        $payloads = $request->only(['name', 'email', 'website']);

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $path = $request->file('logo')->store('logos', 'public');

            $payloads['logo'] = $path;
        }

        Company::create($payloads);

        toast_success('Company successfully created!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateCompanyRequest $request, string $id)
    {
        $company = Company::findOrFail($id);

        $payloads = $request->only(['name', 'email', 'website']);

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            if (!empty($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }

            $path = $request->file('logo')->store('logos', 'public');
            $payloads['logo'] = $path;
        }

        $company->update($payloads);

        toast_success('Company successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }

        toast_success('Company successfully deleted!');
    }
}
