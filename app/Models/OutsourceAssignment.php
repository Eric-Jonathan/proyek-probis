<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutsourceAssignment extends Model
{
    protected $table = 'outsource_assignments';
    protected $primaryKey = 'assignment_id';
    protected $fillable = ['room_id', 'surveyor_id', 'progress', 'assignment_status'];

    // Relasi balik mendapatkan data ruangan yang diajukan
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    // Relasi mendapatkan data user yang bertindak sebagai surveyor
    public function surveyor()
    {
        return $this->belongsTo(People::class, 'surveyor_id', 'user_id');
    }
}
