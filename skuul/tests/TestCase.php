<?php

namespace Tests;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        foreach (['admin', 'teacher', 'student', 'parent'] as $role) {
            Role::findOrCreate($role, config('auth.defaults.guard'));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function createRoleUser(string $role, string $email): User
    {
        $user = User::create([
            'name' => ucfirst($role).' User',
            'email' => $email,
            'password' => Hash::make('password'),
            'role_type' => $role,
            'is_active' => true,
        ]);

        $user->assignRole($role);

        return $user;
    }

    protected function createAcademicYearFixture(): AcademicYear
    {
        return AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
    }

    protected function createTeacherFixture(User $user): Teacher
    {
        return Teacher::create([
            'user_id' => $user->id,
            'employee_id' => 'TCH-FIXTURE-'.$user->id,
            'department' => 'Mathematics',
            'hire_date' => '2026-01-01',
        ]);
    }

    protected function createClassroomFixture(AcademicYear $academicYear, Teacher $teacher): Classroom
    {
        return Classroom::create([
            'name' => 'Grade 10 A',
            'grade_level' => 'Grade 10',
            'section' => 'A',
            'academic_year_id' => $academicYear->id,
            'teacher_id' => $teacher->id,
            'room_number' => '101',
            'capacity' => 30,
        ]);
    }

    protected function createSubjectFixture(Classroom $classroom, Teacher $teacher): Subject
    {
        return Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH-FIXTURE-'.$classroom->id,
            'classroom_id' => $classroom->id,
            'teacher_id' => $teacher->id,
            'credits' => 3,
            'is_compulsory' => true,
        ]);
    }

    protected function createParentFixture(User $user): ParentModel
    {
        return ParentModel::create([
            'user_id' => $user->id,
            'parent_id_code' => 'PAR-FIXTURE-'.$user->id,
            'relationship' => 'father',
        ]);
    }

    protected function createStudentFixture(User $user, ?ParentModel $parent = null): Student
    {
        return Student::create([
            'user_id' => $user->id,
            'student_id' => 'STU-FIXTURE-'.$user->id,
            'grade_level' => 'Grade 10',
            'enrollment_date' => '2026-01-01',
            'parent_id' => $parent?->id,
        ]);
    }
}
