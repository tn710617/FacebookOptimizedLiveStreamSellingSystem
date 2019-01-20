<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StreamingItem extends Model
{
    public function channel ()
    {
        $this->belongsTo('App\Channel');
    }

    public static function checkIfTheFirstStreamingItem($channel_id)
    {
        if(StreamingItem::where('channel_id', $channel_id)->count() < 2)
        {
            return true;
        }
        return false;
    }
}
