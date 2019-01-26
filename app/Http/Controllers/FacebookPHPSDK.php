<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FacebookPHPSDK extends Controller {

    //
    public function getData(Request $request)
    {
        $token = $request->bearerToken();
//        if (isset($request->expirationDate))
//        {
//            $dt = Carbon::parse($request->expirationDate);
//        }
//
//        var_dump($dt->timestamp);
//        var_dump((time()));
//
//        dd('123');
        $fb = new \Facebook\Facebook([
            'app_id'                => '326735094614431',
            'app_secret'            => 'f55684b5977c0c3a684350a06535ee44',
            'default_graph_version' => 'v3.2',
            //'default_access_token' => '{access-token}', // optional
        ]);
        $fb->setExtendedAccessToken();
        $fb->getAccessToken();

// Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
//   $helper = $fb->getRedirectLoginHelper();
//   $helper = $fb->getJavaScriptHelper();
//   $helper = $fb->getCanvasHelper();
//   $helper = $fb->getPageTabHelper();

        try
        {
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            $response = $fb->get('me?fields=id,name,email,picture', $token);
        } catch (\Facebook\Exceptions\FacebookResponseException $e)
        {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e)
        {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }


        $me = $response->getGraphUser();
        dd($me);
        echo 'Logged in as ' . $me->getId();
    }

}
