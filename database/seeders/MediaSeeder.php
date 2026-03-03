<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{
    SocialActivity, 
    Media
};
use Illuminate\Support\Facades\Log;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = SocialActivity::query()
            ->where('type', 'post')
            ->get();

        foreach ($posts as $post) {
            Media::create([
                'filepath' => '/storage/media/2026-03-02/upload-user4-69a54c1fd0699.jpg',
                'user_id' => $post->user_id ?? null,
                'social_activity_id' => $post->id ?? null
            ]);
        }
    }
}
