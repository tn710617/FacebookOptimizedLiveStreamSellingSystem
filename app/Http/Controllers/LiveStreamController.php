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
        $toBeValidatedCondition = ['channel_description' => 'required|string|max:255'];
        $failMessage = Helpers::validation($toBeValidatedCondition, $request);
        if ($failMessage)
        {
            return Helpers::result(false, $failMessage, 400);
        }

        if (User::checkIfUserInAChannel($request))
        {
            return Helpers::result(false, 'The user is already in a channel', 400);
        }

        $host = (new User)->find(User::getUserID($request));
        $channel = new Channel();
        $channel->user_id = $host->id;
        $channel->name = Helpers::createAUniqueNumber();
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
        $current_streaming_items->remaining_quantity = $item->stock;
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
        return Helpers::result(true, 'The designated item is currently on streaming', 200);
    }

    public function join(Request $request)
    {
        if(User::checkIfUserIsAHost($request))
        {
            return Helpers::result(false, 'You are currently holding a live-stream', 400);
        }

        if(!Channel::checkIfChannelExistsWithChannelToken($request))
        {
            return Helpers::result(false, 'The channel doesn\'t exist', 400);
        }

        if(Channel::checkIfChannelHasEnded($request))
        {
            return Helpers::result(false, 'The channel was ended', 400);
        }

        $channel = Channel::where('name', $request->channel_token)
            ->first();
        User::where('id', User::getUserID($request))->update(['channel_id' => $channel->id]);

        return Helpers::result(true, $channel->iFrame, 200);
    }

    public function leave(Request $request)
    {
        if(!User::checkIfUserInAChannel($request))
        {
            return Helpers::result(false, 'You have to be in a channel', 400);
        }
        if(User::checkIfUserIsAHost($request))
        {
            return Helpers::result(false, 'You are currently holding a live-stream', 400);
        }
        User::resetAUserStatus($request);

        return Helpers::result(true, 'You\'ve left the channel', 200);
    }

    public function show(Request $request)
    {
        if(!User::checkIfUserInAChannel($request))
        {
            return Helpers::result(false, 'You have to be in a channel', 400);
        }
        $streaming_items = StreamingItem::getStreamingItems(User::getUser($request)->channel_id);
        if($streaming_items == null)
        {
            return Helpers::result(false, 'You need to stream an item first', 400);
        }

        return Helpers::result(true, [
            'item_id' => $streaming_items->item_id,
            'name' => $streaming_items->item->name,
            'description' => $streaming_items->item->description,
            'unit_price' => $streaming_items->item->unit_price,
            'image' => $streaming_items->item->images,
            'remaining_quantity' => $streaming_items->remaining_quantity,
            'sold_quantity' => $streaming_items->sold_quantity
        ], 200);
    }

    public function end(Request $request)
    {

        if (User::checkIfUserInAChannel($request) && User::checkIfUserIsAHost($request))
        {
            return User::resetUsersStatus($request);
        }
        return Helpers::result(false, 'The user has to be a host and in a channel', 400);
    }

    public function get(Request $request)
    {
        if(!Channel::checkIfChannelExistsWithUserId($request))
        {
            return Helpers::result(true, [], 200);
        }
        $response = [];
        $channels = Channel::where('user_id', User::getUserID($request))->get();
        foreach ($channels as $channel)
        {
            $response[] = [
                'user_id' => $channel->user_id,
                'channel_token' => $channel->name,
                'iFrame' => $channel->iFrame,
                'started_at' => Carbon::parse($channel->started_at)->toCookieString(),
                'ended_at' => Carbon::parse($channel->ended_at)->toCookieString(),
                'channel_description' => $channel->channel_description,
            ];
        }
        return Helpers::result(true, $response, 200);
    }
}
