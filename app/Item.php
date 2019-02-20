<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class Item extends Model {

    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stock', 'name', 'description', 'cost', 'unit_price', 'images', 'status', 'user_id', 'created_at', 'updated_at'
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


    public static function resize($path)
    {
        Image::configure(array('driver' => 'gd'));
        Image::make(storage_path('app/' . $path))->resize(env('ITEM_IMAGE_TO_BE_RESIZED_WIDTH'),env('ITEM_IMAGE_TO_BE_RESIZED_HEIGHT'))->save(storage_path('app/' . $path));
    }

    public static function getImageURL($image)
    {
        return secure_asset('storage/' . substr($image, 7));
    }
}
