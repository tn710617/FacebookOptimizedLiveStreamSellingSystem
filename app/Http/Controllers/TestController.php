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
        $a = 0;
        if ($a === ' ')
        {
            dd(true);
        }
        dd(false);
    }
}
