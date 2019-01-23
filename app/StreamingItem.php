<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StreamingItem extends Model
{
    public function item()
    {
        return $this->belongsTo('App\Item');
    }
    public function channel ()
    {
        return $this->belongsTo('App\Channel');
    }

    public static function checkIfTheFirstStreamingItem($channel_id)
    {
        if(self::where('channel_id', $channel_id)->count() < 2)
        {
            return true;
        }
        return false;
    }
    public static function getStreamingItems($channel_id)
    {
        return self::where('channel_id', $channel_id)->latest()->first();
    }

    public static function checkIfRemainingQuantityEnough($number, $stock)
    {
        if($stock >= $number)
        {
            return true;
        }
        return false;
    }

    public static function updateRemainingQuantity(self $streamingItem, $number)
    {
        $item = $streamingItem->item;
        $stock = $item->stock;
        $item->update(['stock' => $stock - $number]);
        $streamingItem->remaining_quantity = $item->stock;
        $streamingItem->save();
    }
}
