<?php

namespace App\Http\Controllers;

use AllInOne;
use App\Helpers;
use App\Item;
use App\Mail\OrderCreated;
use App\Order;
use App\Token;
use App\User;
use EncryptType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PaymentMethod;

class TestController extends Controller {

    public function test(Request $request)
    {
    }
}
