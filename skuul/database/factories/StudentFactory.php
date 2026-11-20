<?php

namespace Database\Factories;

use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'student_id' => 'STU' . fake()->unique()->numerify('######'),
            'grade_level' => fake()->randomElement(['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12']),
            'enrollment_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'parent_id' => ParentModel::factory(),
            'emergency_contact' => fake()->phoneNumber(),
            'medical_info' => fake()->optional()->sentence(),
        ];
    }
}