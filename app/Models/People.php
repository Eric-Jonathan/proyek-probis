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
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'outsource_id', 'username', 'email', 'phone', 'password', 'role', 'status', 'saldo'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function partner()
    {
        return $this->belongsTo(Outsource::class, 'outsource_id', 'outsource_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'user_id');
    }
}