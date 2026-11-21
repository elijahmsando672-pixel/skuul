<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            AcademicYearSeeder::class,
            UserSeeder::class,
            StudentSeeder::class,
            TeacherSeeder::class,
            ParentSeeder::class,
            ClassroomSeeder::class,
            SubjectSeeder::class,
            EnrollmentSeeder::class,
            TimetableSeeder::class,
            FeeSeeder::class,
            ExamSeeder::class,
            LibrarySeeder::class,
            TransportSeeder::class,
            AttendanceSeeder::class,
            GradeSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}