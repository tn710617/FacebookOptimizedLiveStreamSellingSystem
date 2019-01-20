<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Item;
use App\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class ItemController extends Controller {

    public function create(Request $request)
    {
        $toBeValidated = [
            'name'        => 'required|max:255',
            'description' => 'string|max:255',
            'stock'       => 'required|numeric|digits_between:1,10',
            'cost'        => 'required|numeric|digits_between:1,10',
            'unit_price'  => 'required|numeric|digits_between:1,10',
            'images'      => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
        if ($failMessage = Helpers::validation($toBeValidated, $request))
        {
            return Helpers::result(false, $failMessage, 400);
        }

        $items = new Item();
        $items->name = $request->name;
        $items->description = $request->description;
        $items->stock = $request->stock;
        $items->cost = $request->cost;
        $items->unit_price = $request->unit_price;
        $items->user_id = User::getUserID($request);

        if ($request->hasFile('images'))
        {
            $images = $request->file('images');
            $fileName = time() . '.' . $request->name . '.' . $request->images->getClientOriginalExtension();
            $images->move('../storage/app/public/upload/', $fileName);
            $items->images = $fileName;
            $items->save();
            Image::configure(array('driver' => 'gd'));
            Image::make('../storage/app/public/upload/'.$fileName)->resize(300, 300)->save('../storage/app/public/upload/'.$fileName);
        }

        return Helpers::result(true, 'The item is successfully created', 200);
    }
}
