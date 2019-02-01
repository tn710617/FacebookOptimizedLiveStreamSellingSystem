<?php

namespace App;

use CheckMacValue;
use EncryptType;
use Illuminate\Database\Eloquent\Model;

class PaymentServiceOrders extends Model
{
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
}
