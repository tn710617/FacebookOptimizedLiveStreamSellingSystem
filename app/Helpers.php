<?php


namespace App;


use App\Mail\OrderCreated;
use App\Mail\PaymentReceived;
use App\Mail\PaymentReceivedForSeller;
use App\Mail\PaymentRefunded;
use App\Mail\PaymentRefundedForSeller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class Helpers {

    public static function result($result, $response, $statusCode)
    {
        return Response::json(['result' => $result, 'response' => $response], $statusCode);
    }

    public static function getFacebookResources($token)
    {
        $fb = new \Facebook\Facebook([
            'app_id'                => env('FACEBOOK_API_APP_ID'),
            'app_secret'            => env('FACEBOOK_API_APP_SECRET'),
            'default_graph_version' => env('FACEBOOK_API_DEFAULT_GRAPH_VERSION'),
            //'default_access_token' => '{access-token}', // optional
        ]);

// Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
//   $helper = $fb->getRedirectLoginHelper();
//   $helper = $fb->getJavaScriptHelper();
//   $helper = $fb->getCanvasHelper();
//   $helper = $fb->getPageTabHelper();

        $endpoint = env('FBEndpoint');
        try
        {
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            $response = $fb->get($endpoint, $token);
        } catch (\Facebook\Exceptions\FacebookResponseException $e)
        {
            return false;
//            echo 'Graph returned an error: ' . $e->getMessage();
//            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e)
        {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }


        return $response->getGraphUser();
    }

    public static function validation(Array $toBeValidatedCondition, $toBeValidatedContent)
    {
        $validator = validator::make($toBeValidatedContent->all(), $toBeValidatedCondition);
        if ($validator->fails())
        {
            return $validator->errors()->first();
        }
    }

    public static function createAUniqueNumber()
    {
        $checkTokenCount = 1;
        while ($checkTokenCount)
        {
            $uniqueToken = str_random(6);
            $checkTokenCount = Channel::where('name', $uniqueToken)->count();
        }

        return $uniqueToken;
    }

    public static function customizedPagination($array, Request $request, $per_page)
    {
        $page = Input::get('page', 1); // Get the ?page=1 from the url
        $perPage = $per_page; // Number of items per page
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(
            array_slice($array, $offset, $perPage, true), // Only grab the items we need
            count($array), // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // We need this so we can keep all old query parameters from the url
        );
    }

    public static function convertObjectsToArrays($objects)
    {
        return array_map(function ($object) {
            return (array) $object;
        }, $objects);
    }

    public static function convertStringToIntAmongObjects($objects, $toBeConvertedKeys)
    {
        foreach ($objects as $object)
        {
            foreach ($toBeConvertedKeys as $toBeConvertedKey)
            {
                $object->$toBeConvertedKey = (int) $object->$toBeConvertedKey;
            }
        }

        return $objects;
    }

    public static function checkIfIDExists(Request $request, Model $model, $IDs)
    {
        foreach ($request->$IDs as $ID)
        {
            if ($model::where('id', $ID)->count() == 0)
                return false;
        }

        return true;
    }

    public static function checkIfBelongToTheUser(Request $request, Model $model, $IDs)
    {
        foreach ($request->$IDs as $ID)
        {
            if (User::getUserID($request) !== $model::getUserID($ID))
            {
                return false;
            }
        }

        return true;
    }

    public static function deleteExpiredPaymentOrders(Array $array)
    {
        foreach ($array as $model)
        {
            $toBeDeletedPaymentServiceOrders = $model->where('expiry_time', '<', Carbon::now());
            foreach ($toBeDeletedPaymentServiceOrders->get() as $toBeDeletedPaymentServiceOrder)
            {
                $orderRelations = $toBeDeletedPaymentServiceOrder->orderRelations;
                foreach ($orderRelations as $orderRelation)
                    $orderRelation->delete();
            }
            $toBeDeletedPaymentServiceOrders->delete();
        }
    }

    public static function mailWhenPaid($paymentServiceOrder, $orderRelations)
    {
        $user_id = $paymentServiceOrder->user->id;
        $Seller_user_id = $orderRelations->first()->order->channel->user_id;

        $FB_email = Helpers::getFacebookResources(Token::getLatestToken($user_id))->getEmail();
        $Seller_FB_email = Helpers::getFacebookResources(Token::getLatestToken($Seller_user_id))->getEmail();

        $Seller_local_email = User::where('id', $Seller_user_id)->first()->email;
        $Local_email = User::where('id', $user_id)->first()->email;

        if ($FB_email !== null)
        {
            Mail::to($FB_email)->send(new PaymentReceived($paymentServiceOrder, $orderRelations));
            Mail::to($Seller_FB_email)->send(new PaymentReceivedForSeller($paymentServiceOrder, $orderRelations));
        }

        elseif ($Local_email !== null)
        {
            Mail::to($Local_email)->send(new PaymentReceived($paymentServiceOrder, $orderRelations));
            Mail::to($Seller_local_email)->send(new PaymentReceivedForSeller($paymentServiceOrder, $orderRelations));
        }

    }

    public static function mailWhenRefundedOrReceived($paymentServiceOrder, $orderRelations)
    {
        $user_id = $paymentServiceOrder->user->id;
        $Seller_user_id = $orderRelations->first()->order->channel->user_id;

        $FB_email = Helpers::getFacebookResources(Token::getLatestToken($user_id))->getEmail();
        $Seller_FB_email = Helpers::getFacebookResources(Token::getLatestToken($Seller_user_id))->getEmail();

        $Seller_local_email = User::where('id', $Seller_user_id)->first()->email;
        $Local_email = User::where('id', $user_id)->first()->email;

        if ($FB_email !== null)
        {
            Mail::to($FB_email)->send(new PaymentRefunded($paymentServiceOrder, $orderRelations));
            Mail::to($Seller_FB_email)->send(new PaymentRefundedForSeller($paymentServiceOrder, $orderRelations));
        }

        elseif ($Local_email !== null)
        {
            Mail::to($Local_email)->send(new PaymentRefunded($paymentServiceOrder, $orderRelations));
            Mail::to($Seller_local_email)->send(new PaymentRefundedForSeller($paymentServiceOrder, $orderRelations));
        }
    }


    public static function getLongLivedToken($token)
    {
        $url = 'https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id='.env('FACEBOOK_API_APP_ID').'&client_secret='.env('FACEBOOK_API_APP_SECRET').'&fb_exchange_token='.$token.'';
        return json_decode(file_get_contents($url), true);
    }

    public static function mailWhenOrderPlaced($order, $FB_email, $Local_email)
    {
        if ($FB_email!== null)
            return Mail::to($FB_email)->send(new OrderCreated($order));

        elseif ($Local_email !== null)
            return Mail::to($Local_email)->queue(new OrderCreated($order));
    }


}
