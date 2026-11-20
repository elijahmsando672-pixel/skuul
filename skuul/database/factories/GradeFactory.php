<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        $score = fake()->randomFloat(2, 0, 100);
        $maxScore = 100;

        return [
            'student_id' => Student::factory(),
            'subject_id' => Subject::factory(),
            'classroom_id' => Classroom::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'term_id' => Term::factory(),
            'assessment_type' => fake()->randomElement(['quiz', 'assignment', 'midterm', 'final', 'project']),
            'score' => $score,
            'max_score' => $maxScore,
            'weight' => fake()->randomElement([1.00, 1.50, 2.00]),
            'date_recorded' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}