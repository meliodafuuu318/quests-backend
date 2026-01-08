<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{
    User,
    Friend
};
use Faker\Factory as Faker;

class FriendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::all();
        $userIds = $users->pluck('id');

        foreach ($users as $user) {
            $j = $faker->numberBetween(1, 5);

            for ($i = 0; $i < $j; $i++) {
                $friendId = $faker->randomElement(
                    $userIds->where('id', '!=', $user->id)->values()
                );

                $exists = Friend::where(function ($q) use ($user, $friendId) {
                    $q->where('user_id', $user->id)
                    ->where('friend_id', $friendId);
                })->orWhere(function ($q) use ($user, $friendId) {
                    $q->where('user_id', $friendId)
                    ->where('friend_id', $user->id);
                })->exists();

                if ($exists) {
                    continue;
                }

                $newFriend = Friend::create([
                    'user_id' => $user->id,
                    'friend_id' => $friendId,
                    'status' => $faker->randomElement(['pending_request', 'blocked', 'friend', 'friend', 'friend']),
                ]);

                if ($newFriend->user_id === $newFriend->friend_id) {
                    $newFriend->delete();
                }
            }
        }
    }
}
