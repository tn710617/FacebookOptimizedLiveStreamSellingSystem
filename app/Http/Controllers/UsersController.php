<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Token;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller {

    public function refreshOrCreate(Request $request)
    {
        if (!Token::checkIfTokenReceived($request))
        {
            return Helpers::result(false, 'The token is required', 401);
        }

        if (Token::checkIfTokenExists($request))
        {
            if (Token::checkIfTokenExpired($request))
            {
                Token::where('name', $request->bearerToken())->delete();

                return Helpers::result(false, 'The token has expired', 401);
            }

            return Helpers::result(true, 'The token is effective', 200);
        }

        if (!Token::checkIfTokenValid($request))
        {
            return Helpers::result(false, 'The token is invalid', 401);
        }

        $endpoint = 'me?fields=id,name,email,picture';
        $me = Helpers::getFacebookResources($request->bearerToken(), $endpoint);

        $expiry_time = Helpers::getExpiryTime($request);

        if (Token::checkIfUserExists($me) > 0)
        {
            $user_id = User::getUserIDViaFACEBOOK($me);
            Token::forceCreate([
                'name'        => $request->bearerToken(),
                'user_id'     => $user_id,
                'expiry_time' => $expiry_time
            ]);

            return Helpers::result(true, 'The token is effective', 200);
        }

        $user = new User();
        $user->name = $me->getName();
        $user->FB_id = $me->getId();
        $user->email = $me->getEmail();
        $user->save();

        Token::forceCreate([
            'name'        => $request->bearerToken(),
            'user_id'     => $user->id,
            'expiry_time' => $expiry_time
        ]);

        return Helpers::result(true, 'The token is effective', 200);
    }

    public function get(Request $request)
    {
        $endpoint = 'me?fields=id,name,email,picture';
        $me = Helpers::getFacebookResources($request->bearerToken(), $endpoint);
        $response = ['name' => $me->getName(),
                     'email' => $me->getEmail(),
                     'avatar' => $me->getPicture()['url'],
                    'user_id' => User::getUserID($request)
            ];

        return Helpers::result(true, $response, 200);
    }

    public function getCountryAndPhoneCode()
    {
        $datas = DB::table('country')->get();
        $response = [];
        foreach($datas as $data)
        {
            $response[$data->nicename] = ['country_code' => $data->iso, 'phone_code' => $data->phonecode];
        }
        return $response;
    }

}
