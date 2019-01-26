<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Order extends Model
{
    public function channel()
    {
        return $this->belongsTo('App\Channel');
    }

    public function item()
    {
        return $this->hasOne('App\Item', 'item_id', 'id');
    }
    //
    public static function getOrders(Request $request)
    {
        return Order::where('user_id', User::getUserID($request))->get();
    }
}
