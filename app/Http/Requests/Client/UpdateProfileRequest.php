<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()?->id),
            ],
            'mobile' => [
                'required',
                'string',
                'regex:/^[+]?[0-9]{10,15}$/',
                Rule::unique('users', 'mobile')->ignore($this->user()?->id),
            ],
            'country_id' => ['nullable', 'integer', 'exists:lc_countries,id'],
            'gender' => ['nullable', 'in:male,female'],
            'avatar_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
