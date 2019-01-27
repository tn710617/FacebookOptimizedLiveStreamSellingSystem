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

    public static function checkIfChannelExistsWithChannelToken(Request $request)
    {
        if (Channel::where('name', $request->channel_token)->count() == 1)
        {
            return true;
        }
        return false;
    }

    public static function checkIfChannelHasEnded(Request $request)
    {
        if (Channel::where('name', $request->channel_token)->first()->ended_at !== NULL)
        {
            return true;
        }
        return false;
    }

    public static function checkIfChannelExistsWithUserId(Request $request)
    {
        if ((Channel::where('user_id', User::getUserID($request))->count()) !== 0)
        {
            return true;
        }
        return false;
    }
}
