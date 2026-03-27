<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements BannableContract
{
    use Bannable;
    use HasRoles;
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'national_id',
        'avatar_image',
        'country_id',
        'gender',
        'mobile',
        'is_approved',
        'is_banned',
        'approved_by',
        'created_by',
        'last_login_at',
        'banned_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved'    => 'boolean',
            'is_banned'      => 'boolean',
            'last_login_at'  => 'datetime',
            'country_id'     => 'integer',
            'banned_at'      => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────

   
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }


    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }
    public function approvedClients(): HasMany
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    // Floors created by this manager
    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class, 'manager_id');
    }

    // Rooms created by this manager
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'manager_id');
    }

    // Reservations made by this client
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'client_id');
    }

    // Reservations approved by this receptionist
    public function approvedReservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'receptionist_id');
    }

    public function isCurrentlyBanned(): bool
    {
        return $this->isBanned() || (bool) $this->is_banned;
    }
}
