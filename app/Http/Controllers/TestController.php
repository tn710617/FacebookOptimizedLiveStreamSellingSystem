<?php

namespace App\Http\Controllers;

use App\NewPayPal;
use App\PayPalCheckout;
use Carbon\Carbon;

class TestController extends Controller {
    public function getOrder()
    {
        return PayPalCheckout::getOrder(request()->token);
    }

    public function createOrder()
    {
        return PayPalCheckout::createOrder(request()->intent);
    }

    public function authorizePayPalOrder()
    {
        return PayPalCheckout::authorizeOrder(request()->token, request()->amount);
    }

    public function captureAuthorization()
    {
        return PayPalCheckout::captureAuthorization(request()->token, request()->amount, request()->final_capture);
    }

    public function refundOrder()
    {
        return PayPalCheckout::refundOrder(request()->token, request()->amount);
    }

    public function getAuthorization()
    {
        return PayPalCheckout::getAuthorization(request()->token);
    }

    public function getCapture()
    {
        return PayPalCheckout::getCapture(request()->token);
    }


    public function voidAuthorization()
    {
        return PayPalCheckout::voidAuthorization(request()->token);
    }

    public function reauthorizePayPalOrder()
    {
        return PayPalCheckout::reauthorizeOrder(request()->token);
    }

    public function updateOrder()
    {
        return PayPalCheckout::patchOrder(request()->token);
    }

}
