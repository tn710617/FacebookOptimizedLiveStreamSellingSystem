<?php


namespace App;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class Helpers {
    public static function result($result, $response, $statusCode)
    {
        return Response::json(['result' => $result, 'response' => $response], $statusCode);
    }

    public static function getFacebookResources($token, $endpoint)
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

        try
        {
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            $response = $fb->get($endpoint, $token);
        } catch (\Facebook\Exceptions\FacebookResponseException $e)
        {
            return false;
//            Helpers::result(false, 'The token is invalid', 401);
            // When Graph returns an error
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

    public static function getExpiryTime (Request $request)
    {
        if (isset($request->expirationDate))
        {
            $expiry_time = Carbon::parse($request->expirationDate)->timestamp;
        }
        if (isset($request->expiresIn))
        {
            $expiry_time = time() + $request->expiresIn;
        }

        return $expiry_time;
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

}
