<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'client_id',
        'room_id',
        'receptionist_id',
        'accompany_number',
        'paid_price',
        'check_in_date',
        'check_out_date',
        'status',
        'payment_session_id'
    ];

    public function client()
    {

        return $this->belongsTo(User::class, 'client_id');
    }

    public function room()
    {

        return $this->belongsTo(Room::class, 'room_id');
    }

    public function receptionist()
    {
        return $this->belongsTo(User::class, 'receptionist_id');
    }
}
