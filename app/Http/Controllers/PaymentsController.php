<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Order;
use App\AllPay;
use App\PayPal;
use App\Recipient;
use App\ThirdPartyPaymentService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentsController extends Controller {

    public function listenPayPal(Request $request)
    {
        $PayPal = new PayPal();
        $PayPal->listen($request);
    }

    public function listenAllPay(Request $request)
    {
        $AllPay = new AllPay();
        if($AllPay->listen($request))
            return '1|OK';
    }

    public function pay(Request $request, ThirdPartyPaymentService $thirdPartyPaymentService, Recipient $recipient)
    {
        $toBeValidatedCondition = [
            'order_id' => 'required|array',
        ];
        $failMessage = Helpers::validation($toBeValidatedCondition, $request);
        if ($failMessage)
            return Helpers::result(false, $failMessage, 400);

        if (!Helpers::checkIfIDExists($request, new Order(), 'order_id'))
            return Helpers::result(false, 'The orders doesn\'t exist', 400);

        if (!Helpers::checkIfBelongToTheUser($request, new Order(), 'order_id'))
            return Helpers::result(false, 'The order doesn\'t belong to this user', 400);


        $orders = Order::whereIn('id', $request->order_id)->get();

        if (Order::checkIfOrderPaid($orders))
            return Helpers::result(false, 'The order has already been paid', 400);

        if (Order::checkIfOrderExpired($orders))
            return Helpers::result(false, 'The order has expired', 400);

        if ($recipient->user_id !== User::getUserID($request))
            return Helpers::result(false, 'The recipient doesn\'t belong to the user', 400);

        $toBeSavedInfo = [
            'total_amount' => Order::getTotalAmountForPayments($orders),
            'orders_name' => Order::getOrdersNameForPayments($orders),
            'merchant_trade_no' => time() . Helpers::createAUniqueNumber(),
            'merchant_trade_date' => date('Y/m/d H:i:s'),
            'trade_desc' => 'BuyBuyGo',
            'quantity' => 1,
            'user_id' => User::getUserID($request),
            'payment_service' => $thirdPartyPaymentService,
            'expiry_time' => (new Carbon())->now()->addDay(1)->toDateTimeString(),
            'orders' => $orders,
            'mc_currency' => 'TWD',
            'ClintBackURL' => $request->ClintBackURL
        ];

        switch ($thirdPartyPaymentService->id)
        {
            case 1:
                $error = (new AllPay)->make($toBeSavedInfo, $request, $recipient);
                if($error)
                    return Helpers::result(false, $error,400);

                return (new AllPay())->send($toBeSavedInfo, $request);
                break;

            case 2:
                $error = (new PayPal)->make($toBeSavedInfo, $request, $recipient);
                if($error)
                    return Helpers::result(false, $error, 400);

                $url = (new PayPal)->send($toBeSavedInfo, $request, $recipient);
                return Helpers::result(true, $url, 200);
                break;
        }

    }

    public function getPaymentService()
    {
        return Helpers::result(true, ThirdPartyPaymentService::all(), 200);
    }
}
