<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'kode','booking_date','nokendaraan','namakendaraan','status','user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
