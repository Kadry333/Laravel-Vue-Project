<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReceptionistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'manager']);
    }

    public function rules(): array
    {
        $receptionistId = $this->route('receptionist')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($receptionistId)],
            'national_id' => ['required', 'numeric', 'digits:14', 'starts_with:2,3', Rule::unique('users', 'national_id')->ignore($receptionistId)],
            'avatar_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => strtolower(trim((string) $this->input('email'))),
            'national_id' => $this->filled('national_id') ? trim((string) $this->input('national_id')) : null,
        ]);
    }
}
