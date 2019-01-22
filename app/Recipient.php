<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Recipient extends Model
{
    //
    public static function countRecipientQuantity(Request $request)
    {
        return Recipient::where('user_id', User::getUserID($request))->get()->count();
    }
}
