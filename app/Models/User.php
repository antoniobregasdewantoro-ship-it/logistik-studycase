<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Kolom role yang sudah kamu buat di migration
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi: Satu User (Customer) memiliki banyak Order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}