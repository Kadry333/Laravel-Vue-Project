<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class SuccessReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

 
    public function rules(): array
    {
        return [
            'session_id' => 'required|exists:reservations,payment_session_id'
        ];
    }
}
