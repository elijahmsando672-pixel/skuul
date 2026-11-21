<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::query()->get();
        $subjects = Subject::query()->get();
        $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();

        if ($students->isEmpty() || $subjects->isEmpty() || ! $academicYear) {
            return;
        }

        $types = ['quiz', 'assignment', 'midterm', 'final', 'project'];

        for ($index = 0; $index < 200; $index++) {
            $student = $students->get($index % $students->count());
            $subject = $subjects->get($index % $subjects->count());
            $type = $types[$index % count($types)];

            Grade::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'academic_year_id' => $academicYear->id,
                    'assessment_type' => $type,
                ],
                [
                    'classroom_id' => $subject->classroom_id,
                    'score' => 40 + ($index % 61),
                    'max_score' => 100,
                    'weight' => 1.00,
                    'date_recorded' => now()->subDays($index % 30)->toDateString(),
                ]
            );
        }
    }
}
