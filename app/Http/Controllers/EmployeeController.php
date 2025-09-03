<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Http\Resources\EmployeeCollection;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companies = Company::all(['id', 'name']);

        $query = Employee::with(['company:id,name,email,website,logo']);

        $request->whenFilled('company_id', function ($value) use ($query) {
            $query->where('company_id', $value);
        });
        $request->whenFilled('search', function ($value) use ($query) {
            $query->where(function ($q) use ($value) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($value) . '%'])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($value) . '%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($value) . '%']);
            });
        });

        $employees = $query->paginate($request->get('per_page', 10));

        return Inertia::render('Employees/Index', [
            'filters' => [
                'companies' => $companies
            ],
            'employees' => new EmployeeCollection($employees),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        toast_success('Employee successfully deleted!');
    }
}
