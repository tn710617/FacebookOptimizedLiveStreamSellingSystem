<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $fillable = ['phone_code', 'phone_number'];
    public function recipient()
    {
        return $this->belongsTo('App\Recipient');
    }
    //
}
