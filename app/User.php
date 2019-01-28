<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    public function channel()
    {
        return $this->hasMany('App\Channel', 'user_id', 'id');
    }

    public function order()
    {
        return $this->hasMany('App\Order', 'user_id', 'id');
    }

    public function phone()
    {
        return $this->hasOne('App\Phone', 'id', 'phone_id');
    }

    public function recipient()
    {
        return $this->hasMany('App\Recipient');
    }

    public function item()
    {
        return $this->hasMany('App\Item');
    }


    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'FB_id', 'host', 'id', 'channel_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function getUserIDViaFACEBOOK($FacebookResources)
    {
        return User::where('FB_id', $FacebookResources->getId())->first()->id;
    }

    public static function getUserID(Request $request)
    {
        return Token::where('name', $request->bearerToken())->first()->user_id;
    }

    public static function checkIfUserInAChannel(Request $request)
    {
        if (User::where('id', self::getUserID($request))->first()->channel_id !== 0)
        {
            return true;
        }

        return false;
    }

    public static function checkIfUserIsAHost(Request $request)
    {
        if (self::find(self::getUserID($request))->host == true)
        {
            return true;
        }

        return false;
    }

    public static function getUserChannelId(Request $request)
    {
        return User::find(User::getUserID($request))->channel_id;
    }

    public static function getUser(Request $request)
    {
        return User::find(User::getUserID($request));
    }

    public static function resetAUserStatus(Request $request)
    {
        User::find(User::getUserID($request))->update(['channel_id' => 0]);
    }


    public static function resetUsersStatus(Request $request)
    {
        $seller = User::getUser($request);
        $channel_id = User::getUserChannelId($request);
        $buyers = User::where('channel_id', $channel_id)->get();
        Channel::find($channel_id)->update(['ended_at' => Carbon::now()]);
        foreach ($buyers as $buyer)
        {
            $buyer->update(['channel_id' => 0]);
        }
        $seller->update(['host' => 0]);

        return Helpers::result(true, 'The designated channel has been terminated', 200);
    }

    public static function checkIfUserHasAPhone(Request $request)
    {
        if (User::getUser($request)->phone_id !== null)
        {
            return true;
        }

        return false;
    }

    public function getAllSellerOrders()
    {
        $channels_id = $this->channel->pluck('id');
        $orders = Order::whereIn('channel_id', $channels_id)->get();
        $response = Order::foreachOrders($orders);
        return $response;
    }
}
