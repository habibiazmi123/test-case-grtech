<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\EmployeeCollection;
use App\Models\Company;
use App\Models\Employee;
use App\Notifications\NewEmployeeNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);

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

        $employees = $query->orderBy('first_name', 'asc')->paginate($request->get('per_page', 10));

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
    public function store(CreateEmployeeRequest $request)
    {
        $payloads = $request->only(['company_id', 'first_name', 'last_name', 'email', 'phone']);

        $employee = Employee::create($payloads);

        $company = $employee->company;

        if (!empty($company->email) && filter_var($company->email, FILTER_VALIDATE_EMAIL)) {
            $company->notify(new NewEmployeeNotification($employee));
        }

        toast_success('Employee successfully created!');

        return redirect()->route('employees.index');
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
    public function update(CreateEmployeeRequest $request, string $id)
    {
        $payloads = $request->only(['company_id', 'first_name', 'last_name', 'email', 'phone']);

        $employee = Employee::findOrFail($id);
        $employee->update($payloads);

        toast_success('Employee successfully updated!');

        return redirect()->route('employees.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        toast_success('Employee successfully deleted!');

        return redirect()->route('employees.index');
    }
}
