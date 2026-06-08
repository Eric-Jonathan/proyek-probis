<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutsourceReport extends Model
{
    protected $table = 'outsource_reports';
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'assignment_id', 
        'kondisi', 
        'kebersihan', 
        'catatan', 
        'rekomendasi', 
        'photos', 
        'video', 
        'facilities'
    ];

    protected $casts = [
        'photos' => 'array',
        'facilities' => 'array'
    ];

    public function assignment()
    {
        return $this->belongsTo(OutsourceAssignment::class, 'assignment_id', 'assignment_id');
    }
}
