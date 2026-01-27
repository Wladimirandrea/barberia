<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'imagen',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getImagenAttribute($value)
    {
        return $value ? Storage::disk('public')->url($value) : Storage::disk('public')->url('avatar.png');
    }
}
