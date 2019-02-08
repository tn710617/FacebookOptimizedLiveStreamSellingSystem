<?php

namespace App\Http\Controllers;

use AllInOne;
use App\Helpers;
use App\Mail\PaymentReceived;
use App\Order;
use App\OrderRelations;
use App\PaymentServiceOrders;
use App\ThirdPartyPaymentService;
use App\Token;
use App\User;
use Carbon\Carbon;
use EncryptType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PaymentMethod;

class PaymentsController extends Controller {

    public function receive(Request $request)
    {
        if (PaymentServiceOrders::checkIfCheckMacValueCorrect($request) && PaymentServiceOrders::checkIfPaymentPaid($request->RtnCode))
        {
            $paymentServiceOrder = (new PaymentServiceOrders)->where('MerchantTradeNo', $request->MerchantTradeNo)->first();
            $paymentServiceOrder->update(['status' => 1, 'expiry_time' => null]);

            $orderRelations = $paymentServiceOrder->where('MerchantTradeNo', $request->MerchantTradeNo)->first()->orderRelations;
            Order::updateStatus($orderRelations);

            $user_id = $paymentServiceOrder->user->id;
            $payerEmail = Helpers::getFacebookResources(Token::getLatestToken($user_id))->getEmail();

            if ($payerEmail !== null)
            Mail::to($payerEmail)->send(new PaymentReceived($paymentServiceOrder, $orderRelations));

            return '1|OK';
        }
    }

    public function pay(Request $request, ThirdPartyPaymentService $thirdPartyPaymentService)
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

        $totalAmount = Order::getTotalAmountForPayments($orders);
        $ordersName = Order::getOrdersNameForPayments($orders);
        $MerchantTradeNo = time() . Helpers::createAUniqueNumber();
        $MerchantTradeDate = date('Y/m/d H:i:s');
        $TradeDesc = 'BuyBuyGo';
        $quantity = 1;

        DB::beginTransaction();
        try
        {
            $payment_service_order = new PaymentServiceOrders();

            $payment_service_order->user_id = User::getUserID($request);
            $payment_service_order->payment_service_id = $thirdPartyPaymentService->id;
            $payment_service_order->expiry_time = (new Carbon())->now()->addDay(1)->toDateTimeString();
            $payment_service_order->MerchantID = env('MERCHANTID');
            $payment_service_order->MerchantTradeNo = $MerchantTradeNo;
            $payment_service_order->MerchantTradeDate = $MerchantTradeDate;
            $payment_service_order->TotalAmount = $totalAmount;
            $payment_service_order->TradeDesc = $TradeDesc;
            $payment_service_order->ItemName = $ordersName;
            $payment_service_order->save();

            foreach ($orders as $order)
            {
                $order_relations = new OrderRelations();
                $order_relations->payment_service_id = $thirdPartyPaymentService->id;
                $order_relations->payment_service_order_id = $payment_service_order->id;
                $order_relations->order_id = $order->id;
                $order_relations->save();
            }
        } catch (Exception $e)
        {
            DB::rollBack();

            return Helpers::result('false', 'Something went wrong with DB', 400);
        }
        DB::commit();

        //載入SDK(路徑可依系統規劃自行調整)
        try
        {
            $obj = new AllInOne();

            //服務參數
            $obj->ServiceURL = "https://payment-stage.opay.tw/Cashier/AioCheckOut/V5";         //服務位置
            $obj->HashKey = env('HASHKEY');                                            //測試用Hashkey，請自行帶入AllPay提供的HashKey
            $obj->HashIV = env('HASHIV');                                            //測試用HashIV，請自行帶入AllPay提供的HashIV
            $obj->MerchantID = env('MERCHANTID');                                                      //測試用MerchantID，請自行帶入AllPay提供的MerchantID
            $obj->EncryptType = EncryptType::ENC_SHA256;                                        //CheckMacValue加密類型，請固定填入1，使用SHA256加密

            //基本參數(請依系統規劃自行調整)

            Log::info(env('ALLPAYCLIENTBACKURL'));

            $obj->Send['ReturnURL'] = env('ALLPAYRETURNURL');
            $obj->Send['ClientBackURL'] = $request->ClintBackURL;
            $obj->Send['MerchantTradeNo'] = $MerchantTradeNo;                                 //訂單編號
            $obj->Send['MerchantTradeDate'] = $MerchantTradeDate;                              //交易時間
            $obj->Send['TotalAmount'] = $totalAmount;                                             //交易金額
            $obj->Send['TradeDesc'] = $TradeDesc;                                  //交易描述
            $obj->Send['ChoosePayment'] = PaymentMethod::ALL;                           //付款方式:Credit

            //訂單的商品資料
            array_push($obj->Send['Items'], array('Name'     => $ordersName,
                                                  'Price'    => (int) $totalAmount,
                                                  'Currency' => "元",
                                                  'Quantity' => (int) $quantity,
                                                  'URL'      => "dedwed"));


            # 電子發票參數
            /*
            $obj->Send['InvoiceMark'] = InvoiceState::Yes;
            $obj->SendExtend['RelateNumber'] = $MerchantTradeNo;
            $obj->SendExtend['CustomerEmail'] = 'test@opay.tw';
            $obj->SendExtend['CustomerPhone'] = '0911222333';
            $obj->SendExtend['TaxType'] = TaxType::Dutiable;
            $obj->SendExtend['CustomerAddr'] = '台北市南港區三重路19-2號5樓D棟';
            $obj->SendExtend['InvoiceItems'] = array();
            // 將商品加入電子發票商品列表陣列
            foreach ($obj->Send['Items'] as $info)
            {
                array_push($obj->SendExtend['InvoiceItems'],array('Name' => $info['Name'],'Count' =>
                    $info['Quantity'],'Word' => '個','Price' => $info['Price'],'TaxType' => TaxType::Dutiable));
            }
            $obj->SendExtend['InvoiceRemark'] = '測試發票備註';
            $obj->SendExtend['DelayDay'] = '0';
            $obj->SendExtend['InvType'] = InvType::General;
            */


            //產生訂單(auto submit至AllPay)
            $obj->CheckOut();

        } catch (Exception $e)
        {
            echo $e->getMessage();
        }

    }

    public function getPaymentService()
    {
        return Helpers::result(true, ThirdPartyPaymentService::all(), 200);
    }
}
