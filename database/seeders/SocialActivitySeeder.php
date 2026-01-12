<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{
    SocialActivity,
    User,
    Quest,
    QuestTask,
    QuestParticipant
};
use Faker\Factory as Faker;

class SocialActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $userCount = User::count();
        $posts = SocialActivity::factory()->count($userCount*5)->create();
        $comments = [];

        $users = User::all();
        foreach($posts as $post) {
            $userIds = $users->pluck('id');
            $i = 0;
            $j = $faker->numberBetween(1, 5);
            while ($i < $j) {
                $userId = $faker->randomElement($userIds);
                $user = User::find($userId);
                $comments[] = SocialActivity::create([
                    'user_id' => $userId,
                    'type' => 'comment',
                    'visibility' => 'public',
                    'comment_target' => $post->id,
                    'content' => 'This is a sample comment by ' . $user->username,
                ]);
                $i+=1;
            }

        }

        $posts = collect($posts);
        $comments = collect($comments);

        $mergedContent = $posts->merge($comments);

        foreach ($mergedContent as $content) {
            $userIds = $users->where('id', '!=', $content->user_id)->pluck('id');
            $i = 0;
            $j = $faker->numberBetween(0, 5);
            while ($i < $j) {
                SocialActivity::create([
                    'user_id' => $faker->randomElement($userIds),
                    'type' => 'like',
                    'visibility' => 'public',
                    'like_target' => $content->id
                ]);
                $i+=1;
            }
        }
    }
}
