<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class People extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'people'; 
    protected $primaryKey = 'user_id';
    public $incrementing = true;

    protected $fillable = [
        'username', 'email', 'phone', 'password', 'role'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}