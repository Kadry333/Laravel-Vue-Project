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
            'number'   => ['required', 'string', 'unique:rooms,number'],
            'capacity' => ['required', 'integer', 'min:1'],
            'price'    => ['required', 'integer', 'min:1'],
            'floor_id' => ['required', 'exists:floors,id'],
        ];
    }
}
