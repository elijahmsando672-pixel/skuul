<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        $subjects = Subject::query()->get();
        $students = Student::query()->get();

        if (! $academicYear || $subjects->isEmpty() || $students->isEmpty()) {
            return;
        }

        $types = ['midterm', 'final'];

        foreach ($subjects as $index => $subject) {
            $type = $types[$index % count($types)];

            $exam = Exam::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'subject_id' => $subject->id,
                    'name' => $subject->name.' '.$type.' Exam',
                ],
                [
                    'exam_type' => $type,
                    'exam_date' => now()->addDays($index + 1)->toDateString(),
                    'start_time' => '09:00',
                    'end_time' => '11:00',
                    'total_marks' => 100,
                    'is_published' => true,
                ]
            );

            foreach ($students as $studentIndex => $student) {
                $marks = 40 + (($studentIndex + $index) % 61);

                ExamResult::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'marks_obtained' => $marks,
                        'grade' => $this->gradeFor((float) $marks),
                        'remarks' => null,
                    ]
                );
            }
        }
    }

    private function gradeFor(float $percentage): string
    {
        if ($percentage >= 90) {
            return 'A';
        }

        if ($percentage >= 75) {
            return 'B';
        }

        if ($percentage >= 60) {
            return 'C';
        }

        if ($percentage >= 50) {
            return 'D';
        }

        return 'F';
    }
}
