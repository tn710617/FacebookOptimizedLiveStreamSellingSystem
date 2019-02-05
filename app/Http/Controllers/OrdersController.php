<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Helpers;
use App\Item;
use App\Order;
use App\Recipient;
use App\StreamingItem;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller {

    public function create(Request $request, Item $item, Recipient $recipient)
    {
        $toBeValidatedCondition = [
            'number'   => 'required|integer',
        ];
        $failMessage = Helpers::validation($toBeValidatedCondition, $request);
        if ($failMessage)
        {
            return Helpers::result(false, $failMessage, 400);
        }

        if (!User::checkIfUserInAChannel($request))
            return Helpers::result(false, 'You have to be in a channel', 400);

        if (User::checkIfUserIsAHost($request))
            return Helpers::result(false, 'This operation is only allowed for buyers', 400);

        if (!StreamingItem::checkIfRemainingQuantityEnough($request->number, $item->stock))
            return Helpers::result(false, 'The required quantity is not enough', 400);

        if (!StreamingItem::checkIfAnyItemsOnStream($request))
            return Helpers::result(false, 'There are no any items on stream', 400);

        $buyer = User::getUser($request);

        if ($recipient->user_id !== $buyer->id)
            return Helpers::result(false, 'The recipient doesn\'t belong to the user', 400);

        $streamingItem = StreamingItem::getStreamingItems($buyer->channel_id);

        if ((!StreamingItem::checkIfItemOnStream($streamingItem, $item)))
            return Helpers::result(false, 'The item is not currently on the stream', 400);

        $order = Order::createOrderAndGetInstance($request, $item, $recipient);

        StreamingItem::updateRemainingQuantity($streamingItem, $request->number);

        return Helpers::result(true, 'Your order has been successfully placed', 200);
    }

    public function getBuyerOrders(Request $request)
    {
        $orders = User::getUser($request)->order;
        $response = Order::foreachAndRefineOrders($orders);

        return Helpers::result(true, $response, 200);
    }

    public function getOrdersInLatestChannel(Request $request)
    {
        if (!Order::checkIfUserPlacedOrders($request))
            return Helpers::result(true, [], 200);

        $orders = Order::getOrdersInLatestChannel($request);
        $response = Order::foreachAndRefineOrders($orders);

        return Helpers::result(true, $response, 200);
    }

    public function getSellerOrders(Request $request)
    {
        $response = User::getUser($request)->getAllSellerOrders();

        return Helpers::result(true, $response, 200);

    }

    public function getSellerOrdersPerChannel(Request $request, Channel $channel)
    {
        if ($channel->user_id !== User::getUserID($request))
            return Helpers::result(false, 'Invalid parameters', 400);
        if ($channel->order->count() == 0)
            return Helpers::result(true, '[]', 200);

        $orders = $channel->order;
        $response = Order::foreachAndRefineOrders($orders);

        return Helpers::result(true, $response, 200);
    }

    public function getSoldItems(Request $request)
    {
        $channel_IDs = User::getUser($request)->getAllSellerChannelID();

        $rawInformation = Order::getProfitInDetail($channel_IDs);

        $toBeConverteds = ['cost', 'unit_price', 'profit', 'total_cost', 'quantity', 'turnover'];
        $response = Helpers::convertStringToIntAmongObjects($rawInformation, $toBeConverteds);

        return Helpers::result(true, $response, 200);
    }

    public function getSoldItemsPerChannel(Request $request, Channel $channel)
    {
        $channel_ID = $channel->id;

        $rawInformation = Order::getProfitInDetailPerChannel($channel_ID);

        $toBeConverteds = ['cost', 'unit_price', 'profit', 'total_cost', 'quantity', 'turnover'];
        $response = Helpers::convertStringToIntAmongObjects($rawInformation, $toBeConverteds);

        return Helpers::result(true, $response, 200);
    }

}












