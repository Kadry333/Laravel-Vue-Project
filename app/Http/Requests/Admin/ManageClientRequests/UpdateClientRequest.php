<?php

namespace App\Http\Requests\Admin\ManageClientRequests;

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
            'email' =>  'required|email|unique:users,email,' . $clientId,
            'country_id'   => 'required|integer|exists:lc_countries,id',
            'gender' => 'required|in:male,female',
            'mobile' => 'required|string|unique:users,mobile,' . $clientId,
            'avatar_image' => 'nullable|image|mimes:jpg,jpeg',
        ];
    }
}
