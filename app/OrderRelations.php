<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderRelations extends Model {

    protected $fillable = ['status'];

    public function order()
    {
        return $this->hasOne('App\Order', 'id', 'order_id');
    }

    public function NewPayPal()
    {
        return $this->hasOne('App\NewPayPal', 'id', 'payment_service_order_id');
    }

    public function thirdPartyPaymentService()
    {
        return $this->hasOne('App\ThirdPartyPaymentService', 'id', 'payment_service_id');
    }

    public static function updateStatus($orderRelations, $statusCode)
    {
        foreach ($orderRelations as $orderRelation)
        {
            $orderRelation->update(['status' => $statusCode]);
        }

    }
}
