<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Helpers;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
      public function start(Request $request)
      {
          if(User::checkIfUserInAChannel($request))
          {
              return Helpers::result(false, 'The user is already in a channel',400);
          }

          $host = (new User)->find(User::getUserID($request));
          $channel = new Channel();
          $channel->user_id = $host->id;
          $channel->name = Helpers::createAUniqueChannelToken();
          $channel->iFrame = $request->iFrame;
          $channel->started_at = Carbon::now();
          $channel->channel_description = $request->channel_description;
          $channel->save();

          $host->updateOrCreate(['id' => User::getUserID($request)], ['host' => true, 'channel_id' => $channel->id]);

          return Helpers::result(true, ['channel_id' => $channel->id, 'channel_token' => $channel->name], 200);
      }
}
