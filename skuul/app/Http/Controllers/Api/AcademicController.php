<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function timetables(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Timetable::with(['academicYear', 'classroom', 'subject', 'teacher']);

        switch ($this->roleFor($user)) {
            case 'admin':
                break;
            case 'teacher':
                $teacherId = $user->teacher?->id;
                $query->when(
                    $teacherId,
                    fn (Builder $q) => $q->where('teacher_id', $teacherId),
                    fn (Builder $q) => $q->whereRaw('1 = 0')
                );
                break;
            case 'student':
                $this->scopeByClassrooms($query, $this->classroomIds($user->student), 'classroom_id');
                break;
            case 'parent':
                $this->scopeByClassrooms($query, $this->childClassroomIds($user), 'classroom_id');
                break;
            default:
                $query->whereRaw('1 = 0');
        }

        return response()->json($query->orderBy('day_of_week')->orderBy('start_time')->paginate($this->perPage($request)));
    }

    public function grades(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Grade::with(['student.user', 'subject', 'classroom']);

        switch ($this->roleFor($user)) {
            case 'admin':
                break;
            case 'teacher':
                $teacherId = $user->teacher?->id;
                $query->when(
                    $teacherId,
                    fn (Builder $q) => $q->whereHas('subject', fn (Builder $q) => $q->where('teacher_id', $teacherId)),
                    fn (Builder $q) => $q->whereRaw('1 = 0')
                );
                break;
            case 'student':
                $query->where('student_id', $user->student?->id ?? 0);
                break;
            case 'parent':
                $query->whereIn('student_id', $this->childIds($user) ?: [0]);
                break;
            default:
                $query->whereRaw('1 = 0');
        }

        return response()->json($query->latest('date_recorded')->paginate($this->perPage($request)));
    }

    public function attendance(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Attendance::with(['student.user', 'classroom', 'subject', 'teacher']);

        switch ($this->roleFor($user)) {
            case 'admin':
                break;
            case 'teacher':
                $teacherId = $user->teacher?->id;
                $query->when($teacherId, function (Builder $q) use ($teacherId) {
                    $q->where(function (Builder $q) use ($teacherId) {
                        $q->where('teacher_id', $teacherId)
                            ->orWhereHas('subject', fn (Builder $q) => $q->where('teacher_id', $teacherId));
                    });
                }, fn (Builder $q) => $q->whereRaw('1 = 0'));
                break;
            case 'student':
                $query->where('student_id', $user->student?->id ?? 0);
                break;
            case 'parent':
                $query->whereIn('student_id', $this->childIds($user) ?: [0]);
                break;
            default:
                $query->whereRaw('1 = 0');
        }

        return response()->json($query->latest('date')->paginate($this->perPage($request)));
    }

    public function exams(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = $this->roleFor($user);
        $query = Exam::query();
        $resultScope = null;

        switch ($role) {
            case 'admin':
                break;
            case 'teacher':
                $teacherId = $user->teacher?->id;
                $query->when(
                    $teacherId,
                    fn (Builder $q) => $q->whereHas('subject', fn (Builder $q) => $q->where('teacher_id', $teacherId)),
                    fn (Builder $q) => $q->whereRaw('1 = 0')
                );
                break;
            case 'student':
                $query->where('is_published', true);
                $classroomIds = $this->classroomIds($user->student);
                $subjectIds = $classroomIds ? Subject::whereIn('classroom_id', $classroomIds)->pluck('id')->all() : [];
                $query->whereIn('subject_id', $subjectIds ?: [0]);
                $studentId = $user->student?->id ?? 0;
                $resultScope = fn ($q) => $q->where('student_id', $studentId);
                break;
            case 'parent':
                $query->where('is_published', true);
                $childIds = $this->childIds($user) ?: [0];
                $resultScope = fn ($q) => $q->whereIn('student_id', $childIds);
                break;
            default:
                $query->whereRaw('1 = 0');
        }

        $query->with(array_filter([
            'subject',
            'academicYear',
            'results' => $resultScope ?? fn ($q) => $q,
        ]));

        return response()->json($query->orderByDesc('exam_date')->paginate($this->perPage($request)));
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

    private function scopeByClassrooms(Builder $query, array $classroomIds, string $column): void
    {
        $query->when(
            $classroomIds,
            fn (Builder $q) => $q->whereIn($column, $classroomIds),
            fn (Builder $q) => $q->whereRaw('1 = 0')
        );
    }

    private function classroomIds(?Student $student): array
    {
        if ($student === null) {
            return [];
        }

        return $student->enrollments()->pluck('classroom_id')->filter()->unique()->values()->all();
    }

    private function childIds(User $user): array
    {
        $parent = $user->parent;

        return $parent ? $parent->students()->pluck('id')->all() : [];
    }

    private function childClassroomIds(User $user): array
    {
        $childIds = $this->childIds($user);

        if ($childIds === []) {
            return [];
        }

        return Enrollment::whereIn('student_id', $childIds)
            ->pluck('classroom_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function perPage(Request $request): int
    {
        return max(1, min(100, (int) $request->query('per_page', 15)));
    }
}
