<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Tests\TestCase;

class ExamTest extends TestCase
{
    private User $admin;
    private AcademicYear $academicYear;
    private Teacher $teacher;
    private Classroom $classroom;
    private Subject $subject;
    private User $studentUser;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createRoleUser('admin', 'admin-exam@example.test');
        $this->academicYear = $this->createAcademicYearFixture();

        $teacherUser = $this->createRoleUser('teacher', 'teacher-exam@example.test');
        $this->teacher = $this->createTeacherFixture($teacherUser);

        $this->classroom = $this->createClassroomFixture($this->academicYear, $this->teacher);
        $this->subject = $this->createSubjectFixture($this->classroom, $this->teacher);

        $this->studentUser = $this->createRoleUser('student', 'student-exam@example.test');
        $this->student = $this->createStudentFixture($this->studentUser);
    }

    public function test_admin_can_create_exam_and_save_results(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.exams.store'), [
                'academic_year_id' => $this->academicYear->id,
                'subject_id' => $this->subject->id,
                'name' => 'Midterm Exam',
                'exam_type' => 'midterm',
                'exam_date' => '2026-06-15',
                'total_marks' => 100,
            ])
            ->assertRedirect(route('admin.exams.index'));

        $exam = Exam::where('name', 'Midterm Exam')->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('admin.exams.results.store', $exam), [
                'results' => [
                    [
                        'student_id' => $this->student->id,
                        'marks_obtained' => 92,
                        'remarks' => 'Excellent work',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.exams.results', $exam));

        $this->assertDatabaseHas('exam_results', [
            'exam_id' => $exam->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 92,
            'grade' => 'A',
            'remarks' => 'Excellent work',
        ]);
    }

    public function test_grade_is_computed_from_marks(): void
    {
        $exam = $this->createExam('Grading Exam');

        $marks = [95, 80, 65, 55, 40];
        $grades = ['A', 'B', 'C', 'D', 'F'];
        $students = [];

        foreach ($marks as $index => $mark) {
            $user = $this->createRoleUser('student', 'grading-student-'.$index.'@example.test');
            $students[] = $this->createStudentFixture($user);
        }

        $results = collect($students)->map(function (Student $student, int $index) use ($marks) {
            return [
                'student_id' => $student->id,
                'marks_obtained' => $marks[$index],
            ];
        })->all();

        $this->actingAs($this->admin)
            ->post(route('admin.exams.results.store', $exam), ['results' => $results])
            ->assertRedirect(route('admin.exams.results', $exam));

        foreach ($students as $index => $student) {
            $this->assertSame(
                $grades[$index],
                ExamResult::where('exam_id', $exam->id)
                    ->where('student_id', $student->id)
                    ->value('grade')
            );
        }
    }

    public function test_student_exams_page_only_shows_published_exams(): void
    {
        $published = $this->createExam('Published Exam', true);
        $draft = $this->createExam('Draft Exam', false);

        ExamResult::create([
            'exam_id' => $published->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 90,
            'grade' => 'A',
        ]);

        ExamResult::create([
            'exam_id' => $draft->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 60,
            'grade' => 'C',
        ]);

        $this->actingAs($this->studentUser)
            ->get(route('student.exams.index'))
            ->assertOk()
            ->assertViewHas('results', function ($results) {
                return $results->count() === 1
                    && $results->first()->exam->name === 'Published Exam';
            })
            ->assertSee('Published Exam')
            ->assertDontSee('Draft Exam');
    }

    private function createExam(string $name, bool $published = false): Exam
    {
        return Exam::create([
            'academic_year_id' => $this->academicYear->id,
            'subject_id' => $this->subject->id,
            'name' => $name,
            'exam_type' => 'midterm',
            'exam_date' => '2026-06-15',
            'total_marks' => 100,
            'is_published' => $published,
        ]);
    }
}
