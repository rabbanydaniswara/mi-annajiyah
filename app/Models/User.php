<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['username', 'password', 'role', 'active_session_id'];

    protected $hidden = ['password', 'remember_token', 'active_session_id'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
