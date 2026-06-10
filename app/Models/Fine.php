<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    use HasFactory;

    protected $table = 'fines';
    protected $primaryKey = 'fine_id';

    protected $fillable = [
        'booking_id',
        'jenis_denda',
        'nominal_denda',
        'keterangan',
        'bukti_denda',
        'status',
        'is_dismissed',
        'is_paid'
    ];

    protected $casts = [
        'bukti_denda' => 'array',
    ];

    // Relasi ke Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
