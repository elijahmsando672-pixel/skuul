<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\FeeRecord;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->is_platform_admin) {
            return response()->json(['role' => 'platform_admin', 'data' => $this->platformData()]);
        }
        $role = $this->roleFor($user);

        $data = match ($role) {
            'admin' => $this->adminData(),
            'teacher' => $this->teacherData($user),
            'student' => $this->studentData($user),
            'parent' => $this->parentData($user),
            default => [],
        };


        return response()->json(['role' => $role, 'data' => $data]);
    }

    private function roleFor(User $user): string
    {
        foreach (['admin', 'teacher', 'student', 'parent'] as $role) {
            if ($user->hasRole($role)) {
                return $role;
            }
        }

        return 'guest';
    }

    private function adminData(): array
    {
        return [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_classrooms' => Classroom::count(),
            'total_subjects' => Subject::count(),
            'active_academic_years' => AcademicYear::active()->count(),
            'published_announcements' => Announcement::where('is_published', true)->count(),
            'published_exams' => Exam::where('is_published', true)->count(),
            'fee_records' => FeeRecord::count(),
            'books' => Book::count(),
            'active_book_issues' => BookIssue::where('status', BookIssue::STATUS_ISSUED)->count(),
            'recent_announcements' => Announcement::with('user')->latest()->limit(5)->get(),
        ];
    }

    private function teacherData(User $user): array
    {
        $teacher = $user->teacher;
        $subjectIds = $teacher ? $teacher->subjects()->pluck('id')->all() : [];
        $classroomIds = $teacher ? $teacher->subjects()->pluck('classroom_id')->filter()->unique()->all() : [];

        return [
            'teacher_id' => $teacher?->id,
            'subjects' => count($subjectIds),
            'students' => $classroomIds
                ? Student::whereHas('enrollments', fn ($query) => $query->whereIn('classroom_id', $classroomIds))->count()
                : 0,
            'attendance_marked' => $teacher ? Attendance::where('teacher_id', $teacher->id)->count() : 0,
            'grades_recorded' => $subjectIds ? Grade::whereIn('subject_id', $subjectIds)->count() : 0,
            'timetable_slots' => $teacher ? Timetable::where('teacher_id', $teacher->id)->count() : 0,
            'recent_grades' => $subjectIds
                ? Grade::with(['student.user', 'subject'])->whereIn('subject_id', $subjectIds)->latest()->limit(5)->get()
                : collect(),
        ];
    }

    private function studentData(User $user): array
    {
        $student = $user->student;
        $studentId = $student?->id;
        $classroomIds = $student ? $student->enrollments()->pluck('classroom_id')->filter()->unique()->all() : [];
        $attendance = $studentId ? Attendance::where('student_id', $studentId)->get() : collect();
        $feeRecords = $studentId ? FeeRecord::where('student_id', $studentId)->get() : collect();

        return [
            'student_id' => $studentId,
            'attendance_total' => $attendance->count(),
            'attendance_present' => $attendance->where('status', Attendance::STATUS_PRESENT)->count(),
            'grades' => $studentId ? Grade::where('student_id', $studentId)->count() : 0,
            'fee_balance' => round((float) $feeRecords->sum(fn (FeeRecord $record) => $record->balance), 2),
            'timetable_slots' => $classroomIds ? Timetable::whereIn('classroom_id', $classroomIds)->count() : 0,
            'recent_grades' => $studentId
                ? Grade::with(['subject'])->where('student_id', $studentId)->latest()->limit(5)->get()
                : collect(),
            'recent_attendance' => $attendance->sortByDesc('date')->take(5)->values(),
        ];
    }

    private function parentData(User $user): array
    {
        $parent = $user->parent;
        $childIds = $parent ? $parent->students()->pluck('id')->all() : [];
        $feeRecords = $childIds
            ? FeeRecord::whereIn('student_id', $childIds)->get()
            : collect();

        return [
            'children' => count($childIds),
            'attendance_records' => $childIds ? Attendance::whereIn('student_id', $childIds)->count() : 0,
            'fee_records' => $feeRecords->count(),
            'fee_balance' => round((float) $feeRecords->sum(fn (FeeRecord $record) => $record->balance), 2),
            'recent_announcements' => Announcement::where('is_published', true)
                ->whereIn('target_audience', [Announcement::TARGET_ALL, Announcement::TARGET_PARENTS])
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }

    private function platformData(): array
    {
        return [
            'total_schools' => \App\Models\School::count(),
            'active_schools' => \App\Models\School::where('status','active')->count(),
            'pending_schools' => \App\Models\School::where('status','pending')->count(),
            'active_subscriptions' => \App\Models\Subscription::whereIn('status',['active','trial'])->count(),
        ];
    }
}

