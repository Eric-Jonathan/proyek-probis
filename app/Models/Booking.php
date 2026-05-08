<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'user_id',
        'total',
        'method_payment',
        'photo',
        'start_date',
        'end_date',
        'status',
    ];

    // Relasi ke User/People (Penyewa)
    public function user()
    {
        return $this->belongsTo(People::class, 'user_id', 'user_id');
    }

    // Relasi ke Detail Booking (Satu booking memiliki satu detail ruangan)
    public function details()
    {
        return $this->hasOne(BookingDetail::class, 'booking_id', 'booking_id');
    }
}