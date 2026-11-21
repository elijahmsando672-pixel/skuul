<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::inRandomOrder()->first() ?? Student::factory(),
            'classroom_id' => Classroom::inRandomOrder()->first() ?? Classroom::factory(),
            'subject_id' => Subject::inRandomOrder()->first() ?? Subject::factory(),
            'teacher_id' => Teacher::inRandomOrder()->first() ?? Teacher::factory(),
            'date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'status' => fake()->randomElement(['present', 'absent', 'late', 'excused']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
