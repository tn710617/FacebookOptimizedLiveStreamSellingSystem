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

        $total_cost = $item->cost * $request->number;
        $total_amount = $item->unit_price * $request->number;
        $profit = $total_amount - $total_cost;
        $orderName = time() . Helpers::createAUniqueNumber();
        Order::forceCreate([
            'name'               => $orderName,
            'user_id'            => $buyer->id,
            'item_name'          => $item->name,
            'item_description'   => $item->description,
            'unit_price'         => $item->unit_price,
            'cost'               => $item->cost,
            'total_cost'         => $item->cost * $request->number,
            'profit'             => $profit,
            'quantity'           => $request->number,
            'total_amount'       => $total_amount,
            'channel_id'         => $buyer->channel_id,
            'images'             => $item->images,
            'recipient'          => $recipient->name,
            'phone_code'         => $recipient->phone->phone_code,
            'phone_number'       => $recipient->phone->phone_number,
            'post_code'          => $recipient->postcode,
            'country'            => DB::table('country')->where('iso', $recipient->country_code)->first()->nicename,
            'city'               => $recipient->city,
            'district'           => $recipient->district,
            'others'             => $recipient->others,
            'expiry_time'        => Carbon::now()->addDays(3)->toDateTimeString(),
            'to_be_deleted_time' => Carbon::now()->addDays(6)->toDateTimeString(),
        ]);


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












