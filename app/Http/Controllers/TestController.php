<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Token;
use App\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function test(Request $request)
    {
        return $host = (new User)->find(User::getUserID($request))->id;
        dd(User::getUserID($request));
       dd($user = (new User)->find(User::getUserID($request)));
    }
}
