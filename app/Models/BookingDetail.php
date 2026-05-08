<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingDetail extends Model
{
    use HasFactory;

    protected $table = 'booking_details';

    protected $primaryKey = 'bd_id';

    protected $fillable = [
        'booking_id',
        'item_id',
        'item_type',
        'item_price',
        'status',
    ];

    // Relasi balik ke Header Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    // Relasi ke Room (Item yang disewa)
    public function room()
    {
        return $this->belongsTo(Room::class, 'item_id', 'room_id');
    }
}