<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'number',
        'capacity',
        'price',
        'floor_id',
        'manager_id'
    ];



    public function floor()
    {

        return $this->belongsTo(Floor::class, 'floor_id');
    }
   public function manager()
    {

        return $this->belongsTo(User::class, 'manager_id');
    }

    public function reservations()
    {

        return $this->hasMany(Reservation::class, 'room_id');
    }
}
