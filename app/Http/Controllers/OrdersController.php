<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Helpers;
use App\Item;
use App\Order;
use App\StreamingItem;
use App\User;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function create(Request $request, Item $item)
    {
        if(! StreamingItem::checkIfRemainingQuantityEnough($request->number, $item->stock))
        {
            return Helpers::result(false, 'The required quantity is not enough', 400);
        }
        $orderName = time().Helpers::createAUniqueNumber();
        $buyer = User::getUser($request);
        Order::forceCreate([
            'name' => $orderName,
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'unit_price' => $item->unit_price,
            'quantity' => $request->number,
            'total_amount' => $item->unit_price * $request->number,
            'channel_id' => $buyer->channel_id
        ]);

        $streamingItem = StreamingItem::getStreamingItems($buyer->channel_id);

        StreamingItem::updateRemainingQuantity($streamingItem, $request->number);
        return Helpers::result(true, 'Your order has been successfully placed', 200);
    }
}
