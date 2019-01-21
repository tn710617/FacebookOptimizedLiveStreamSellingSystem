<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Channel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ended_at',
    ];

    public function streaming_item()
    {
        return $this->hasMany('App\StreamingItem');
    }
}
