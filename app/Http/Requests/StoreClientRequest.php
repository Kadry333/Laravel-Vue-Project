<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' =>  'required|email|unique:users,email',
            'password' =>'required|string|confirmed|min"6',
            'country' => 'required|string|exists:lc_countries,official_name',
            'gender' => 'required|in:male,female',
            'mobile' => 'required|string|unique:users,mobile',
            'avatar_image' => 'nullable|image|mimes:jpg,jpeg',
        ];
    }
}
