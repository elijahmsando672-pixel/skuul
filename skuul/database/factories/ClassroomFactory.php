<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'grade_level' => fake()->randomElement(['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12']),
            'section' => fake()->randomElement(['A', 'B', 'C']),
            'academic_year_id' => AcademicYear::factory(),
            'teacher_id' => Teacher::factory(),
            'room_number' => fake()->numerify('R##'),
            'capacity' => fake()->numberBetween(20, 40),
        ];
    }
}