<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialActivity>
 */
class SocialActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::role('USER');
        $userId = fake()->randomElement($users->pluck('id'));
        $user = User::find($userId);
        return [
            'user_id' => $userId,
            'type' => 'post',
            'visibility' => fake()->randomElement(['public', 'friends']),
            'title' => 'This is a sample post by ' . $user->username
        ];
    }
}
