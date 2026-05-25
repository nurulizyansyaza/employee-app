<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'min:1', 'max:30'],
            'nik'       => ['required', 'string', 'size:10', 'unique:employees,nik'],
            'birthdate' => ['required', 'date', 'before_or_equal:' . now()->subYears(15)->toDateString()],
            'sex'       => ['required', 'boolean'],
            'address'   => ['nullable', 'string', 'max:200'],
            'salary'    => ['required', 'numeric', 'min:0'],
            'currency'  => ['sometimes', 'string', 'size:3', 'regex:/^[A-Za-z]{3}$/'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'birthdate.before_or_equal' => 'Employee must be at least 15 years old.',
            'nik.size'                  => 'NIK must be exactly 10 digits.',
            'nik.unique'                => 'NIK already exists.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sex'       => filter_var($this->input('sex'), FILTER_VALIDATE_BOOLEAN),
            'is_active' => $this->has('is_active')
                ? filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN)
                : true,
        ]);
    }
}
