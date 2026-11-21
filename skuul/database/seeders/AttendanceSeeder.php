<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::query()->get();
        $classrooms = Classroom::query()->get();
        $subjects = Subject::query()->get();
        $teachers = Teacher::query()->get();

        if ($students->isEmpty() || $classrooms->isEmpty() || $teachers->isEmpty()) {
            return;
        }

        for ($index = 0; $index < 100; $index++) {
            Attendance::firstOrCreate(
                [
                    'student_id' => $students->get($index % $students->count())->id,
                    'date' => now()->subDays($index % 30)->toDateString(),
                    'classroom_id' => $classrooms->get($index % $classrooms->count())->id,
                ],
                [
                    'subject_id' => $subjects->isNotEmpty() ? $subjects->get($index % $subjects->count())->id : null,
                    'teacher_id' => $teachers->get($index % $teachers->count())->id,
                    'status' => ['present', 'present', 'present', 'absent', 'late'][$index % 5],
                ]
            );
        }
    }
}
