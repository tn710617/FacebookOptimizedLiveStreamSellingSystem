<?php

namespace App;

use Carbon\Carbon;
use CheckMacValue;
use EncryptType;
use Illuminate\Database\Eloquent\Model;

class PaymentServiceOrders extends Model
{
    protected $fillable = ['status', 'expiry_time'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function orderRelations()
    {
        return $this->hasMany('App\OrderRelations', 'payment_service_order_id', 'id');
    }

    public static function checkIfPaymentPaid($RtnCode)
    {
        if($RtnCode == 1)
            return true;
        return false;
    }

    public static function checkIfCheckMacValueCorrect($paymentResponse)
    {
        $parameters = $paymentResponse->except('CheckMacValue');
        $receivedCheckMacValue = $paymentResponse->CheckMacValue;
        $calculatedCheckMacValue = CheckMacValue::generate($parameters, env('HASHKEY'), env('HASHIV'), EncryptType::ENC_SHA256);
        if($receivedCheckMacValue == $calculatedCheckMacValue)
            return true;
        return false;
    }

    public static function deleteExpiredOrders()
    {
        $toBeDeletedPaymentServiceOrders = (new PaymentServiceOrders)->where('expiry_time', '<', Carbon::now());
        foreach ($toBeDeletedPaymentServiceOrders->get() as $toBeDeletedPaymentServiceOrder)
        {
            $orderRelations = $toBeDeletedPaymentServiceOrder->orderRelations;
            foreach ($orderRelations as $orderRelation)
                $orderRelation->delete();
        }
        $toBeDeletedPaymentServiceOrders->delete();
    }
}
