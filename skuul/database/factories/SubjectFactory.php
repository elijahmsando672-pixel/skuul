<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Mathematics', 'English', 'Physics', 'Chemistry', 'Biology', 'History', 'Geography', 'Computer Science', 'Art', 'Music']),
            'code' => fake()->unique()->bothify('SUB??##'),
            'description' => fake()->sentence(),
            'classroom_id' => Classroom::factory(),
            'teacher_id' => Teacher::factory(),
            'credits' => fake()->randomElement([2, 3, 4]),
            'is_compulsory' => fake()->boolean(80),
        ];
    }
}