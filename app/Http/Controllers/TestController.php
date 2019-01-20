<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Item;
use App\Token;
use App\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {
        if(Item::checkIfAnyItemUploaded($request))
        {
            return Helpers::result(true, "This user hasn't uploaded any items", 200);
        }

        $items = User::find(User::getUserID($request))->item->all();
        $response = [];
        foreach($items as $item)
        {
            $withoutImages = $item->only('name', 'description', 'stock', 'cost', 'unit_price');
            $addedImagesLink = array_add($withoutImages, 'images', asset('storage/upload/'.$item->images));
            $response[$item->id] = $addedImagesLink;
        }
        return $response;
    }
}
