<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $rooms = Room::all();

        Message::factory(100)->make()->each(function (Message $message) use ($users, $rooms) {
            $message->user()->associate($users->random());
            $message->room()->associate($rooms->random());
            $message->save();
        });
    }
}
