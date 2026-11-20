<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'user_id' => User::factory()->admin(),
            'target_audience' => fake()->randomElement(['all', 'students', 'teachers', 'parents', 'admin']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'starts_at' => fake()->optional()->dateTimeBetween('-1 month', '+1 month'),
            'expires_at' => fake()->optional()->dateTimeBetween('+1 month', '+6 months'),
            'is_published' => true,
        ];
    }
}