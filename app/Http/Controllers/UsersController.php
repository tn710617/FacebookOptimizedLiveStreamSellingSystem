<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Phone;
use App\Recipient;
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
        $response = ['name'    => $me->getName(),
                     'email'   => $me->getEmail(),
                     'avatar'  => $me->getPicture()['url'],
                     'user_id' => User::getUserID($request)
        ];

        return Helpers::result(true, $response, 200);
    }

    public function getCountryAndPhoneCode()
    {
        $datas = DB::table('country')->get();
        $response = [];
        foreach ($datas as $data)
        {
            $response[$data->nicename] = ['country_code' => $data->iso, 'phone_code' => $data->phonecode];
        }

        return $response;
    }

    public function createNewRecipients(Request $request)
    {
        $toBeValidatedCondition = [
            'name'                 => 'required|string|max:255',
            'phone.phone_code'     => 'required|string|max:5',
            'phone.phone_number'   => 'required|string|min:5|max:20',
            'address.country_code' => 'required|size:2',
            'address.post_code'    => 'required|max:10',
            'address.city'         => 'required|string|max:50',
            'address.district'     => 'required|string|max:50',
            'address.others'       => 'required|string|max:255'
        ];
        $failMessage = Helpers::validation($toBeValidatedCondition, $request);
        if ($failMessage)
        {
            return Helpers::result(false, $failMessage, 400);
        }

        if (Recipient::countRecipientQuantity($request) == User::getUser($request)->recipient_quantity)
        {
            return Helpers::result(false, 'You\'ve reached your recipient\'s quantity limit', 400);
        }

        $phone = new Phone();
        $phone->phone_code = $request->phone['phone_code'];
        $phone->phone_number = $request->phone['phone_number'];
        $phone->save();

        Recipient::forceCreate([
            'name'         => $request->name,
            'phone_id'     => $phone->id,
            'user_id'      => User::getUserID($request),
            'postcode'     => $request->address['post_code'],
            'country_code' => $request->address['country_code'],
            'city'         => $request->address['city'],
            'district'     => $request->address['district'],
            'others'       => $request->address['others']
        ]);

        return Helpers::result(true, ['recipient_limit' => User::getUser($request)->recipient_quantity, 'created_recipients' => Recipient::countRecipientQuantity($request)], 200);
    }

}
