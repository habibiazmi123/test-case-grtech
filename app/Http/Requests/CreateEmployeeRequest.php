<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('employee');

        return [
            'company_id' => 'required|exists:companies,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => [
                'nullable',
                'email',
                $id
                    ? Rule::unique('employees', 'email')->ignore($id)
                    : Rule::unique('employees', 'email')
            ],
            'phone' => 'nullable|regex:/^\+?[0-9]{10,15}$/',
        ];
    }
}
