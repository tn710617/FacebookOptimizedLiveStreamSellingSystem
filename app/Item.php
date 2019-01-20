<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Item extends Model
{
    //
    public static function checkIfAnyItemUploaded(Request $request)
    {
        $items = (User::find(User::getUserID($request))->item);
        return $items->isEmpty();
    }
}
