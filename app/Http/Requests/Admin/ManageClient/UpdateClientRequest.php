<?php

namespace App\Http\Requests\Admin\ManageClient;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
          $clientId = $this->route('client')->id;
        return [
            'name' => 'required|string|max:255',
            'email' =>  'required|email|max:255|unique:users,email,' . $clientId,
            'country_id'   => 'required|integer|exists:lc_countries,id',
            'gender' => 'required|in:male,female',
            'mobile' => 'regex:/^[+]?[0-9]{10,15}$/|unique:users,mobile,' . $clientId,
            'avatar_image' => 'nullable|image|mimes:jpg,jpeg|max:2048',
        ];
    }
}
