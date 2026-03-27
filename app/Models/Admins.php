<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'national_id',
        'avatar_image'
    ];

    public function approvedClients()
    {

        $this->hasMany(Client::class, 'approved_by');
    }

    public function floors()
    {
        return $this->hasMany(Floor::class, 'admin_id');
    }

    public function rooms()
    {

        return $this->hasMany(Room::class, 'admin_id');
    }
}
