<?php

namespace App\Http\Requests\Admin\Floor;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFloorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => trim($this->name),
        ]);
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('floor');
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('floors', 'name')->ignore($id),
            ],
            'manager_id' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);

                    if (!$user) {
                        return $fail('Selected manager does not exist.');
                    }

                    if (!$user->hasRole('manager')) {
                        return $fail('Selected user is not a manager.');
                    }
                }
            ],
        ];

    }
}
