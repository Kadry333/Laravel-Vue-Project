<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'client_id',
        'room_id',
        'accompany_number',
        'paid_price',
        'handled_by'
    ];

    public function handledBy()
    {

        return $this->belongsTo(Admin::class, 'handled_by');
    }

    public function client()
    {

        return $this->belongsTo(Client::class, 'client_id');
    }

    public function room()
    {

        return $this->belongsTo(Room::class, 'room_id');
    }
}
