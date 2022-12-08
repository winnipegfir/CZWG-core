<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Users\User;

class DiscordController extends Controller
{
    public function getDiscordMembers()
    {
        $discord_users = User::where('discord_user_id', '!=', null)->get();
        $response = [];

        foreach ($discord_users as $user) {
            $roster = $user->rosterProfile()->first();

            $response[] = [
                'discord_id' => $user->discord_user_id,
                'full_name' => $user->fullName('FLC'),
                'is_home_controller' => $roster != null && $roster->visit != 1,
                'is_visiting_controller' => $roster != null && $roster->visit == 1,
                'rating' => $user->rating_short,
            ];
        }

        return response()->json($response);
    }
}
