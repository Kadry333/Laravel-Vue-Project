<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_image',
        'country',
        'gender',
        'is_approved',
        'approved_by'
    ];

    public function approvedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function reservations()
    {

        return $this->hasMany(Reservation::class, 'client_id');
    }
}
