<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outsource extends Model
{
    protected $table = 'outsources';
    protected $primaryKey = 'outsource_id';
    
    protected $fillable = [
        'company_name', 'nib', 'npwp', 'business_type', 
        'company_address', 'pic_name', 'pic_position', 'pic_email', 'pic_phone',
        'bank_name', 'bank_account', 'status'
    ];

    public function account()
    {
        return $this->hasOne(People::class, 'outsource_id', 'outsource_id');
    }
}
