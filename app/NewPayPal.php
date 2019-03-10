<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PayPalCheckoutSdk\Orders\OrdersAuthorizeRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class NewPayPal extends Model {

    protected $fillable = ['status', 'expiry_time', 'approve_date', 'to_be_captured_date', 'authorization_expiry_date', 'to_be_captured+amount', 'authorization_id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function orderRelations()
    {
        return $this->hasMany('App\OrderRelations', 'payment_service_order_id', 'id');
    }

    public function recipient()
    {
        return $this->hasOne('App\Recipient', 'id', 'recipient_id');
    }


    public function make(Array $toBeSavedInfo, Request $request, Recipient $recipient)
    {
        DB::beginTransaction();
        try
        {
            $NewPayPal = new self();

            $NewPayPal->user_id = $toBeSavedInfo['user_id'];
            $NewPayPal->payment_service_id = $toBeSavedInfo['payment_service']->id;
            $NewPayPal->expiry_time = $toBeSavedInfo['expiry_time'];
            $NewPayPal->merchant_trade_no = $toBeSavedInfo['merchant_trade_no'];
            $NewPayPal->total_amount = $toBeSavedInfo['total_amount'];
            $NewPayPal->trade_desc = $toBeSavedInfo['trade_desc'];
            $NewPayPal->item_name = $toBeSavedInfo['orders_name'];
            $NewPayPal->mc_currency = $toBeSavedInfo['mc_currency'];
            $NewPayPal->recipient_id = $recipient->id;
            $NewPayPal->payment_id = $toBeSavedInfo['payment_id'];
            $NewPayPal->client_back_url = request()->ClientBackURL;
            $NewPayPal->save();

            foreach ($toBeSavedInfo['orders'] as $order)
            {
                $order_relations = new OrderRelations();
                $order_relations->payment_service_id = $toBeSavedInfo['payment_service']->id;
                $order_relations->payment_service_order_id = $NewPayPal->id;
                $order_relations->order_id = $order->id;
                $order_relations->save();
            }
        } catch (Exception $e)
        {
            DB::rollBack();

            return 'something went wrong with DB';
        }
        DB::commit();
    }


    /**
     * Setting up the JSON request body for creating the Order. The Intent in the
     * request body should be set as "CAPTURE" for capture intent flow.
     *
     */
    public static function buildRequestBody($toBeSavedInfo, Recipient $recipient)
    {
        $item = [];
        $i = 1;
        foreach ($toBeSavedInfo['orders'] as $order)
        {
            $item[] = [
                'name'        => $order->item_name,
                'description' => $order->item_description,
                'sku'         => $i,
                'unit_amount' => [
                    'currency_code' => $toBeSavedInfo['mc_currency'],
                    'value'         => $order->total_amount,
                ],
                'quantity'    => $order->quantity,
            ];
            $i ++;
        }

        return [
            'intent'              => env('PAYPAL_SANDBOX_INTENT_OF_CREATED_ORDERS'),
            'application_context' =>
                [
                    'return_url'           => env('PAYPAL_SANDBOX_RETURN_URL'),
                    'cancel_url'           => env('PAYPAL_SANDBOX_CANCEL_URL'),
                    'brand_name'           => env('APP_NAME'),
                    'locale'               => env('PAYPAL_SANDBOX_LOCALE'),
                    'landing_page'         => env('PAYPAL_SANDBOX_LANDING_PAGE'),
                    'shipping_preferences' => env('PAYPAL_SANDBOX_SHIPPING_PREFERENCES'),
                    'user_action'          => env('PAYPAL_SANDBOX_USER_ACTION'),
                ],
            'purchase_units'      =>
                [
                    [
                        'custom_id' => $toBeSavedInfo['merchant_trade_no'],
                        'amount'    =>
                            [
                                'currency_code' => $toBeSavedInfo['mc_currency'],
                                'value'         => $toBeSavedInfo['total_amount'],
                                'breakdown'     =>
                                    [
                                        'item_total' =>
                                            [
                                                'currency_code' => $toBeSavedInfo['mc_currency'],
                                                'value'         => $toBeSavedInfo['total_amount'],
                                            ],
                                    ],
                            ],

                        'items'    => $item,
                        'shipping' =>
                            array(
                                'name'    =>
                                    array(
                                        'full_name' => $recipient->name,
                                    ),
                                'address' =>
                                    array(
                                        'address_line_1' => $recipient->others,
                                        'admin_area_2'   => $recipient->district,
                                        'admin_area_1'   => $recipient->city,
                                        'postal_code'    => $recipient->postcode,
                                        'country_code'   => $recipient->country_code,
                                    ),
                            ),
                    ],
                ],
        ];
    }

    /**
     * This is the sample function which can be sued to create an order. It uses the
     * JSON body returned by buildRequestBody() to create an new Order.
     */
    public function createOrder($toBeSavedInfo, Recipient $recipient, $debug = false)
    {
        $request = new OrdersCreateRequest();
        $request->headers["prefer"] = "return=representation";
        $request->body = self::buildRequestBody($toBeSavedInfo, $recipient);

        $client = PayPalClient::client();
        $response = $client->execute($request);
        if ($debug)
        {
            print "Status Code: {$response->statusCode}\n";
            print "Status: {$response->result->status}\n";
            print "Order ID: {$response->result->id}\n";
            print "Intent: {$response->result->intent}\n";
            print "Links:\n";
            foreach ($response->result->links as $link)
            {
                print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
            }
            // To toggle printing the whole response body comment/uncomment below line
            echo json_encode($response->result, JSON_PRETTY_PRINT), "\n";
        }
//        return ['response' => $response];
        foreach (($response->result->links) as $link)
        {
            if ($link->rel === 'approve')
            {
                $linkForApproval = $link->href;
                break;
            }
        }

        $toBeSavedInfo['payment_id'] = $response->result->id;
        $toBeSavedInfo['statusCode'] = $response->statusCode;
        $toBeSavedInfo['custom_id'] = $response->result->purchase_units[0]->custom_id;
        $toBeSavedInfo['PayPal_total_amount'] = $response->result->purchase_units[0]->amount->value;
        $toBeSavedInfo['orderStatus'] = $response->result->status;
        $toBeSavedInfo['linkForApproval'] = $linkForApproval;

        return $toBeSavedInfo;
    }

    /**
     * This function can be used to perform authorization on the approved order.
     * Valid Approved order id should be passed as an argument.
     */
    public static function authorizeOrder($orderId, $amount = null, $debug = false)
    {
        $request = new OrdersAuthorizeRequest($orderId);
        $request->body = self::buildRequestBodyForAuthorizeOrder($amount);

        $client = PayPalClient::client();
        $response = $client->execute($request);
        if ($debug)
        {
            print "Status Code: {$response->statusCode}\n";
            print "Status: {$response->result->status}\n";
            print "Order ID: {$response->result->id}\n";
            print "Authorization ID: {$response->result->purchase_units[0]->payments->authorizations[0]->id}\n";
            print "Links:\n";
            foreach ($response->result->links as $link)
            {
                print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
            }
            print "Authorization Links:\n";
            foreach ($response->result->purchase_units[0]->payments->authorizations[0]->links as $link)
            {
                print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
            }
            // To toggle printing the whole response body comment/uncomment below line
            echo json_encode($response->result, JSON_PRETTY_PRINT), "\n";
        }

        return $response;
    }

    public static function buildRequestBodyForAuthorizeOrder($amount = null)
    {
        if ($amount != null)
        {
            return [
                "amount" => [
                    'currency_code' => 'USD',
                    'value'         => $amount,
                ],
            ];
        }

        return "{}";
    }

    public static function checkIfPaymentIDExists()
    {
        if ((NewPayPal::where('payment_id', request()->token)->count()) == 1)
        {
            return true;
        }

        return false;
    }

    public static function checkIfAuthorizedSuccessfully($response)
    {
        $newPayPal = (new NewPayPal())->where('payment_id', request()->token)->first();
        if (($response->result->status) !== 'COMPLETED')
            return 'Authorization isn\'t completed';

        if (($response->result->purchase_units[0]->payments->authorizations[0]->status) !== 'CREATED')
            return 'Authorization was not created';

        if (($response->result->purchase_units[0]->payments->authorizations[0]->amount->currency_code) !== ($newPayPal->mc_currency))
            return 'The currency is mismatched';

        if (intval($response->result->purchase_units[0]->payments->authorizations[0]->amount->value) !== ($newPayPal->total_amount))
            return 'The total amount is not correct';
    }

    public function listen($response)
    {
        $failedMessage = NewPayPal::checkIfAuthorizedSuccessfully($response);
        if ($failedMessage)
            return $failedMessage;

        $NewPayPalOrder = (new NewPayPal())->where('payment_id', request()->token)->first();
        $NewPayPalOrder->update([
            'status'                    => 6,
            'expiry_time'               => null,
            'approve_date'              => (new Carbon())->now()->toDateTimeString(),
            'to_be_captured_date'       => (new Carbon())->now()->addDays(2)->toDateTimeString(),
            'authorization_expiry_date' => Carbon::parse($response->result->purchase_units[0]->payments->authorizations[0]->expiration_time)->toDateTimeString(),
            'to_be_captured_amount'     => $response->result->purchase_units[0]->payments->authorizations[0]->amount->value,
            'authorization_id'          => $response->result->purchase_units[0]->payments->authorizations[0]->id,
            'to_be_completed_date'      => (new Carbon())->now()->addDays(7)->toDateTimeString(),
        ]);
        $recipient = $NewPayPalOrder->recipient;

        $orderRelations = $NewPayPalOrder->orderRelations->where('payment_service_id', 3);

        Order::updateStatus($orderRelations, $recipient);

        Helpers::mailWhenPaid($NewPayPalOrder, $orderRelations);
    }

    public static function checkIfPaymentApproved()
    {
        if ((NewPayPal::where('payment_id', request()->token)->first()->approve_date) !== null)
        {
            return true;
        }

        return false;
    }
}
