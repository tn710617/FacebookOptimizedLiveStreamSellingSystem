<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Helpers;
use App\Item;
use App\StreamingItem;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LiveStreamController extends Controller {

    public function start(Request $request)
    {
        if (User::checkIfUserInAChannel($request))
        {
            return Helpers::result(false, 'The user is already in a channel', 400);
        }

        $host = (new User)->find(User::getUserID($request));
        $channel = new Channel();
        $channel->user_id = $host->id;
        $channel->name = Helpers::createAUniqueChannelToken();
        $channel->iFrame = $request->iFrame;
        $channel->started_at = Carbon::now();
        $channel->channel_description = $request->channel_description;
        $channel->save();

        $host->updateOrCreate(['id' => User::getUserID($request)], ['host' => true, 'channel_id' => $channel->id]);

        return Helpers::result(true, ['channel_id' => $channel->id, 'channel_token' => $channel->name], 200);
    }

    public function streamAnItem(Request $request, Item $item)
    {
        if (!User::checkIfUserIsAHost($request))
        {
            return Helpers::result(false, 'You are only allowed to do this operation when you are a host', 400);
        }

        $current_streaming_items = new StreamingItem();
        $current_streaming_items->item_id = $item->id;
        $current_streaming_items->quantity = $item->stock;
        $current_streaming_items->channel_id = User::getUserChannelId($request);
        $current_streaming_items->started_at = Carbon::now();
        $current_streaming_items->save();

        if(! StreamingItem::checkIfTheFirstStreamingItem(User::getUserChannelId($request)))
        {
            $old_streaming_items_id = (new StreamingItem())
                ->where('channel_id', User::getUserChannelId($request))
                ->latest()
                ->offset(1)
                ->skip(1)
                ->take(1)
                ->first()->id;

            StreamingItem::where('id', $old_streaming_items_id)
                ->update(['ended_at' => $current_streaming_items->started_at]);

            return Helpers::result(true, 'The designated item is currently on streaming', 200);
        }
    }
}
