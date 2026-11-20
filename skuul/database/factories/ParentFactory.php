<?php

namespace Database\Factories;

use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParentFactory extends Factory
{
    protected $model = ParentModel::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->parent(),
            'parent_id_code' => 'PAR' . fake()->unique()->numerify('######'),
            'occupation' => fake()->jobTitle(),
            'relationship' => fake()->randomElement(['father', 'mother', 'guardian']),
        ];
    }
}