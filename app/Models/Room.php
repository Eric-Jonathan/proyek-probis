<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $primaryKey = 'room_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'name', 'capacity', 'deposit_percent', 'price', 
        'description', 'status', 'location', 'latitude', 
        'longitude', 'rules', 'user_id', 'jenis_harga', 'min_order'
    ];

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'item_id', 'room_id');
    }

    public function images()
    {
        return $this->hasMany(RoomImage::class, 'room_id', 'room_id');
    }
}