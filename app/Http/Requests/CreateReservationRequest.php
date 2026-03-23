<?php

namespace App\Http\Requests;

use App\Cores\General\RepositoryInterfaces\RoomRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class CreateReservationRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roomRepository = App::make(RoomRepositoryInterface::class);
        $room = $roomRepository->find($this->room_id);

        return [
            'room_id' => ['required', 'exists:rooms,id'],
            'accompany_number' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($room) {
                    if ($room->reservations()->exists()) {
                        $fail("Sorry, this room is currently not available for booking.");
                    }

                    if ($value > $room->capacity) {
                        $fail("The accompany number cannot exceed the room capacity ({$room->capacity}).");
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required' => 'Please select a room to proceed.',
            'room_id.exists' => 'The selected room does not exist.',
            'accompany_number.required' => 'Accompany number is required.',
            'accompany_number.integer' => 'Accompany number must be a valid number.',
            'accompany_number.min' => 'Accompany number cannot be less than 0.',
        ];
    }
}
