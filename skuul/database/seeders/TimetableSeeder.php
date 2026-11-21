<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\Timetable;
use Illuminate\Database\Seeder;

class TimetableSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        $subjects = Subject::with('classroom', 'teacher')->orderBy('id')->limit(5)->get();

        if (! $year || $subjects->isEmpty()) {
            return;
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $slots = [
            ['start' => '08:00', 'end' => '09:00'],
            ['start' => '09:15', 'end' => '10:15'],
            ['start' => '10:30', 'end' => '11:30'],
            ['start' => '11:45', 'end' => '12:45'],
            ['start' => '13:30', 'end' => '14:30'],
        ];

        foreach ($days as $dayIndex => $day) {
            foreach ($subjects as $subjectIndex => $subject) {
                $slot = $slots[$subjectIndex % count($slots)];

                Timetable::updateOrCreate(
                    [
                        'classroom_id' => $subject->classroom_id,
                        'day_of_week' => $day,
                        'start_time' => $slot['start'],
                    ],
                    [
                        'academic_year_id' => $year->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $subject->teacher_id,
                        'end_time' => $slot['end'],
                        'room' => 'Room '.($dayIndex + 1).sprintf('%02d', $subjectIndex + 1),
                    ]
                );
            }
        }
    }
}
