<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    protected $primaryKey = 'rating_id';

    protected $fillable = [
        'booking_id',
        'item_id',
        'item_type',
        'kebersihan',
        'pelayanan',
        'kenyamanan'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}