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
        if (User::checkIfUserIsAHost($request))
        {
            return 'true';
        }
        return 'false';
    }
}
