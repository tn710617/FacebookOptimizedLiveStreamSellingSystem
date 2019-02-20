<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Item;
use App\Recipient;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ItemsController extends Controller {

    public function create()
    {
        $toBeValidated = [
            'name'        => 'required|max:255',
            'description' => 'max:255',
            'stock'       => 'required|numeric|digits_between:1,10',
            'cost'        => 'required|numeric|digits_between:1,10',
            'unit_price'  => 'required|numeric|digits_between:1,10',
            'images'      => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
        if ($failMessage = Helpers::validation($toBeValidated, request()))
        {
            return Helpers::result(false, $failMessage, 400);
        }

        $parameters = request()->all();

        if (request()->hasFile('images'))
        {
            $parameters['images'] = request()->file('images')->store('public/upload');
        }

        $parameters['user_id'] = User::getUserID(request());

        $item = Item::create($parameters);

        if ($item->images)
            Item::resize($item->images);

        return Helpers::result(true, 'The item is successfully created', 200);
    }

    public function get(Request $request)
    {
        if (Item::checkIfAnyItemUploaded($request))
        {
            return Helpers::result(true, [], 200);
        }

        $items = Item::orderBy('created_at', 'desc')->where('user_id', User::getUserID($request))->get();
        $response = [];
        foreach ($items as $item)
        {
            $withoutImages = $item->only(['id', 'name', 'description', 'stock', 'cost', 'unit_price']);
            $addedImagesLink = array_add($withoutImages, 'images', $item->images == null ? null : Item::getImageURL($item->images));
            $response[] = $addedImagesLink;
        }

        return Helpers::result(true, $response, 200);
    }


    public function update(Request $request, Item $item)
    {
        $toBeValidated = [
            'name'        => 'required|max:255',
            'description' => 'max:255',
            'stock'       => 'required|numeric|digits_between:1,10',
            'cost'        => 'required|numeric|digits_between:1,10',
            'unit_price'  => 'required|numeric|digits_between:1,10',
            'images'      => 'image|mimes:jpeg,png,jpg,gif,svg|max:6144',
        ];
        if ($failMessage = Helpers::validation($toBeValidated, $request))
        {
            return Helpers::result(false, $failMessage, 400);
        }

        $parameters = request()->all();

        if (request()->hasFile('images'))
        {
            Storage::delete($item->images);
            $parameters['images'] = request()->file('images')->store('public/upload');
        }

        $parameters['user_id'] = User::getUserID(request());

        $item->update($parameters);

        if ($item->images)
            Item::resize($item->images);

        if ($request->imageDelete == true)
        {
            Storage::delete($item->images);
            $item->update(['images' => null]);
        }

        return Helpers::result(true, 'The item is successfully updated', 200);
    }

    public function destroy(Request $request)
    {
        if (!Helpers::checkIfIDExists($request, new Item, 'items'))
            return Helpers::result(false, 'Invalid parameters', 400);
        if (!Helpers::checkIfBelongToTheUser($request, new Item(), 'items'))
            return Helpers::result(false, 'Invalid parameters', 400);

        Item::destroy($request->items);

        return Helpers::result(true, 'The item has been successfully deleted', 200);
    }

}
