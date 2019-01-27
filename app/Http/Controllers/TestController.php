<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Item;
use App\Token;
use App\User;
use Illuminate\Http\Request;

class TestController extends Controller {

    public function test(Request $request)
    {
        $channels = User::getUser($request)->channel;
        $response = [];
        foreach ($channels as $channel)
        {
            $orders = $channel->order;
            foreach ($orders as $order)
                $response[] = [
                    'order'   => $order->name,
                    'user_id' => $order->id,
                    'name' => $order->item_name,
                    'description' => $order->item_description,
                    'unit_price' => $order->unit_price,
                    'quantity' => $order->quantity,
                    'total_amount' => $order->amount,
                    'channel_id' => $order->channel_id,
                    'status' => $order->status,
                    'time' => $order->created_at->toCookieString(),
                    'images' => $order->images == NULL ? NULL : secure_asset('storage/upload/'.$order->images)
                ];
        }
        return Helpers::result(true, $response, 200);
    }
}
