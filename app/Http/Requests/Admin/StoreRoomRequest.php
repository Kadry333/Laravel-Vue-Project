<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number'   => ['required', 'integer', 'min:1000', 'unique:rooms,number'],

            'capacity' => ['required', 'integer', 'min:1'],

            'price'    => ['required', 'numeric', 'min:0'],

            'floor_id' => ['required', 'exists:floors,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.min'    => 'Room number must be at least 4 digits (1000 or more).',
            'number.unique' => 'This room number is already taken.',
        ];
    }
}
