<?php

namespace Alyani\Subsystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Manager extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'family',
        'mobile',
        'email',
        'password',
        'avatarSID',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->family;
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'avatarSID', 'SID');
    }
}
