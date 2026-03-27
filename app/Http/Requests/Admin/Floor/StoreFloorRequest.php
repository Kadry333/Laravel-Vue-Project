<?php

namespace App\Http\Requests\Admin\Floor;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFloorRequest extends FormRequest
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
        return [
            'name' => 'required|unique:floors,name|string|min:3|max:255',
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
