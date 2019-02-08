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
            if (Token::checkIfTokenExpired($request->bearerToken()))
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

        $me = Helpers::getFacebookResources($request->bearerToken());

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

        User::updateOrCreate(['FB_id' => $me->getId()], [
            'id'          => 0,
            'name'        => $me->getName(),
            'email'       => $me->getEmail(),
            'expiry_time' => $expiry_time
        ]);

//        $user = new User();
//        $user->name = $me->getName();
//        $user->FB_id = $me->getId();
//        $user->email = $me->getEmail();
//        $user->save();

        Token::forceCreate([
            'name'        => $request->bearerToken(),
            'user_id'     => User::getUserIDViaFACEBOOK($me),
            'expiry_time' => $expiry_time
        ]);

        return Helpers::result(true, 'The token is effective', 200);
    }

    public function get(Request $request)
    {
        $me = Helpers::getFacebookResources($request->bearerToken());
        $response = ['name'    => $me->getName(),
                     'email'   => $me->getEmail(),
                     'avatar'  => 'https://graph.facebook.com/' . User::getUser($request)->FB_id . '/picture?type=large',
                     'user_id' => User::getUserID($request),
                     'phone'   => 'NULL'
        ];
        if (User::checkIfUserHasAPhone($request))
        {
            $phone = User::getUser($request)->phone;
            $response = array_replace($response, ['phone' => [
                'phone_code'   => $phone->phone_code,
                'phone_number' => $phone->phone_number
            ]]);
        }

        return Helpers::result(true, $response, 200);
    }

    public function getCountryAndPhoneCode()
    {
        $datas = DB::table('country')->get();
        $response = [];
        foreach ($datas as $data)
        {
            $response[] = ['country' => $data->nicename, 'country_code' => $data->iso, 'phone_code' => $data->phonecode];
        }

        return Helpers::result(true, $response, 200);
    }

    public function createNewRecipients(Request $request)
    {
        $toBeValidatedCondition = [
            'name'                 => 'required|string|max:255',
            'phone'                => 'required|array',
            'phone.phone_code'     => 'required|string|max:5',
            'phone.phone_number'   => 'required|string|min:5|max:20',
            'address'              => 'required|array',
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

    public function getRecipients(Request $request)
    {
        if (Recipient::countRecipientQuantity($request) == 0)
        {
            return Helpers::result(false, 'This user hasn\'t had any recipient\'s information yet', 400);
        }
        $recipients = Recipient::where('user_id', User::getUserID($request))->get();
        $response = [];
        foreach ($recipients as $recipient)
        {
            $information = [
                'recipient_id' => $recipient->id,
                'name'         => $recipient->name,
                'phone'        => Phone::find($recipient->phone_id)->only('phone_code', 'phone_number'),
                'address'      =>
                    [
                        'country_code' => $recipient->country_code,
                        'post_code'    => $recipient->postcode,
                        'city'         => $recipient->city,
                        'district'     => $recipient->district,
                        'others'       => $recipient->others
                    ]
            ];
            $response[] = $information;
        }

        return Helpers::result(true, $response, 200);
    }

    public function updateRecipients(Request $request, Recipient $recipient)
    {
        if (Recipient::countRecipientQuantity($request) == 0)
        {
            return Helpers::result(false, 'This user hasn\'t had any recipient\'s information yet', 400);
        }
        $toBeValidatedCondition = [
            'name'                 => 'required|string|max:255',
            'phone'                => 'required|array',
            'phone.phone_code'     => 'required|string|max:5',
            'phone.phone_number'   => 'required|string|min:5|max:20',
            'address'              => 'required|array',
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

        $recipient->phone->update([
            'phone_code'   => $request->phone['phone_code'],
            'phone_number' => $request->phone['phone_number']
        ]);

        $recipient->update([
            'name'         => $request->name,
            'postcode'     => $request->address['post_code'],
            'country_code' => $request->address['country_code'],
            'city'         => $request->address['city'],
            'district'     => $request->address['district'],
            'others'       => $request->address['others']
        ]);

        return Helpers::result(true, 'The recipient\'s information has been successfully updated', 200);
    }

    public function destroyRecipients(Request $request)
    {
        if (!Helpers::checkIfIDExists($request, new Recipient(), 'recipients'))
            return Helpers::result(false, 'Invalid parameters', 400);
        if (!Helpers::checkIfBelongToTheUser($request, new Recipient(), 'recipients'))
            return Helpers::result(false, 'Invalid parameters', 400);

        Recipient::destroy($request->recipients);

        return Helpers::result(true, 'The recipient has been successfully deleted', 200);
    }

    public function update(Request $request)
    {
        $toBeValidatedCondition = [
            'phone'                => 'required|array',
            'phone.phone_code'     => 'required|string|max:5',
            'phone.phone_number'   => 'required|string|min:5|max:20',
        ];
        $failMessage = Helpers::validation($toBeValidatedCondition, $request);
        if ($failMessage)
        {
            return Helpers::result(false, $failMessage, 400);
        }

        $user = User::getUser($request);
        if (User::checkIfUserHasAPhone($request))
        {
            $user->phone->update([
                'phone_code'   => $request->phone['phone_code'],
                'phone_number' => $request->phone['phone_number']
            ]);
        }
        $phone = new Phone();
        $phone->forceCreate([
            'phone_code'   => $request->phone['phone_code'],
            'phone_number' => $request->phone['phone_number']
        ]);
        $user->update(['phone_id' => $phone->id]);

        return Helpers::result(true, 'User\'s information has been successfully updated', 200);
    }

    public function getTaiwanPostCode(Request $request)
    {
        $response = DB::table('zipcode')->select('City', 'Area', 'ZipCode')->get();

        return Helpers::result(true, $response, 200);
    }
}
