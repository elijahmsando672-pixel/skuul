<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\User;
use Tests\TestCase;

class TimetableTest extends TestCase
{
    private User $admin;
    private AcademicYear $academicYear;
    private Teacher $teacher;
    private Classroom $classroom;
    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createRoleUser('admin', 'admin-timetable@example.test');
        $this->academicYear = $this->createAcademicYearFixture();

        $teacherUser = $this->createRoleUser('teacher', 'teacher-timetable@example.test');
        $this->teacher = $this->createTeacherFixture($teacherUser);

        $this->classroom = $this->createClassroomFixture($this->academicYear, $this->teacher);
        $this->subject = $this->createSubjectFixture($this->classroom, $this->teacher);
    }

    public function test_admin_can_create_timetable_entry_and_it_appears_in_index(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.timetables.store'), $this->payload())
            ->assertRedirect(route('admin.timetables.index'));

        $this->assertDatabaseHas('timetables', [
            'classroom_id' => $this->classroom->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:00',
            'room' => 'Room 101',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.timetables.index'))
            ->assertOk()
            ->assertSee('Monday')
            ->assertSee('Room 101');
    }

    public function test_duplicate_classroom_day_and_start_time_is_rejected(): void
    {
        Timetable::create($this->payload());

        $this->actingAs($this->admin)
            ->from(route('admin.timetables.create'))
            ->post(route('admin.timetables.store'), $this->payload())
            ->assertRedirect(route('admin.timetables.create'))
            ->assertSessionHasErrors('start_time');

        $this->assertSame(1, Timetable::count());
    }

    public function test_student_timetable_page_loads_for_student_with_classroom(): void
    {
        $studentUser = $this->createRoleUser('student', 'student-timetable@example.test');
        $student = $this->createStudentFixture($studentUser);

        Enrollment::create([
            'student_id' => $student->id,
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->academicYear->id,
            'enrollment_date' => '2026-01-01',
            'status' => 'active',
        ]);

        Timetable::create($this->payload());

        $this->actingAs($studentUser)
            ->get(route('student.timetable.index'))
            ->assertOk()
            ->assertViewIs('student.timetable.index')
            ->assertViewHas('student')
            ->assertSee('My Timetable');
    }

    private function payload(): array
    {
        return [
            'academic_year_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'room' => 'Room 101',
        ];
    }
}
