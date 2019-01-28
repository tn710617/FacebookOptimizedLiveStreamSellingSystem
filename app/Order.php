<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Order extends Model {

    public function channel()
    {
        return $this->belongsTo('App\Channel', 'channel_id', 'id');
    }

    public function item()
    {
        return $this->hasOne('App\Item', 'item_id', 'id');
    }

    public static function getOrders(Request $request)
    {
        return Order::where('user_id', User::getUserID($request))->get();
    }

    public static function getBuyerLatestChannel(Request $request)
    {
        return Order::where('user_id', User::getUserID($request))->latest()->first()->channel;
    }

    public static function getAllPlacedOrdersForBuyer(Request $request)
    {
        $orders = Order::getOrders($request);
        $response = Order::foreachOrders($orders);

        return $response;
    }

    public static function checkIfUserPlacedOrders(Request $request)
    {
        if (Order::where('user_id', User::getUserID($request))->count() !== 0)
        {
            return true;
        }

        return false;
    }

    public static function foreachOrders($orders)
    {
        foreach ($orders as $order)
        {
            $response[] = [
                'order'        => $order->name,
                'user_id'      => $order->id,
                'name'         => $order->item_name,
                'description'  => $order->item_description,
                'unit_price'   => $order->unit_price,
                'quantity'     => $order->quantity,
                'total_amount' => $order->total_amount,
                'channel_id'   => $order->channel_id,
                'status'       => $order->status,
                'time'         => $order->created_at->toCookieString(),
                'images'       => $order->images == null ? null : secure_asset('storage/upload/' . $order->images)
            ];
        }

        return $response;
    }


    public static function getOrdersInLatestChannel(Request $request)
    {
        $latestOrder = Order::where('user_id', User::getUserID($request))->latest()->first();

        return Order::where('channel_id', $latestOrder->channel_id)->where('user_id', User::getUserID($request))->get();
    }
}
