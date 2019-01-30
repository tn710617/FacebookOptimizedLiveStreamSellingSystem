<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Recipient extends Model
{
    protected $fillable = ['name', 'postcode', 'country_code', 'city', 'district', 'others'];
    public function phone()
    {
        return $this->hasOne('App\Phone', 'id', 'phone_id');
    }
    //
    public static function countRecipientQuantity(Request $request)
    {
        return static::where('user_id', User::getUserID($request))->get()->count();
    }

    public static function getUserID($recipient)
    {
        return static::where('id', $recipient)->first()->user_id;
    }
}
