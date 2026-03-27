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

            'check_in_date'  => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'accompany_number' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($room) {
                    if ($value > $room->capacity) {
                        $fail("The accompany number cannot exceed the room capacity ({$room->capacity}).");
                    }
                },
            ],
            'room_id' => [
                'required',
                'exists:rooms,id',
                function ($attribute, $value, $fail) {
                    $overlap = \App\Models\Reservation::where('room_id', $value)
                        ->where('check_in_date', '<', $this->check_out_date)
                        ->where('check_out_date', '>', $this->check_in_date)
                        ->exists();

                    if ($overlap) {
                        $fail('This room is already booked for the selected dates.');
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
