<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Item extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stock'
    ];


    public function streamingItem()
    {
        return $this->hasMany('App\StreamingItem');
    }
    public static function checkIfAnyItemUploaded(Request $request)
    {
        $items = (User::find(User::getUserID($request))->item);
        return $items->isEmpty();
    }

    public static function getUserID($item_id)
    {
        return static::where('id', $item_id)->first()->user_id;
    }

}
