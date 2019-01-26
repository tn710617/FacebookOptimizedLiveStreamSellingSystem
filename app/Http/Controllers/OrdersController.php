<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Item;
use App\Order;
use App\StreamingItem;
use App\Token;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;

class OrdersController extends Controller
{
    public function create(Request $request, Item $item)
    {
        if(!User::checkIfUserInAChannel($request))
            return Helpers::result(false, 'You have to be in a channel', 400);

        if(User::checkIfUserIsAHost($request))
            return Helpers::result(false, 'This operation is only allowed for buyers', 400);

        if(! StreamingItem::checkIfRemainingQuantityEnough($request->number, $item->stock))
            return Helpers::result(false, 'The required quantity is not enough', 400);

        $orderName = time().Helpers::createAUniqueNumber();
        $buyer = User::getUser($request);
        Order::forceCreate([
            'name' => $orderName,
            'user_id' => $buyer->id,
            'item_name' => $item->name,
            'item_description' => $item->description,
            'unit_price' => $item->unit_price,
            'quantity' => $request->number,
            'total_amount' => $item->unit_price * $request->number,
            'channel_id' => $buyer->channel_id,
            'images' => $item->images
        ]);

        $streamingItem = StreamingItem::getStreamingItems($buyer->channel_id);

        StreamingItem::updateRemainingQuantity($streamingItem, $request->number);
        return Helpers::result(true, 'Your order has been successfully placed', 200);
    }

    public function get(Request $request)
    {

        $orders = Order::getOrders($request);
        $response = [];
        foreach($orders as $order)
        {
            $response[] = [
                'order' => $order->name,
                'name' => $order->item_name,
                'description' => $order->item_description,
                'unit_price' => $order->unit_price,
                'quantity' => $order->quantity,
                'total_amount' => $order->total_amount,
                'channel_id' => $order->channel_id,
                'status' => $order->status,
                'time' => $order->created_at->toCookieString(),
                'images' => $order->images == NULL ? NULL :secure_asset('storage/upload'.$order->images)
            ];
        }

        return Helpers::result(true, $response, 200);
    }

}












