<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function teacherIndex()
    {
        $teacher = auth()->user()->teacher;
        $subjects = $teacher ? $teacher->subjects()->with('classroom')->get() : collect();
        $classrooms = $subjects->pluck('classroom')->filter()->unique('id')->values();
        return view('attendance.teacher-index', compact('teacher', 'subjects', 'classrooms'));
    }

    public function studentIndex()
    {
        $student = auth()->user()->student;
        $attendance = $student ? Attendance::where('student_id', $student->id)->latest()->paginate(15) : collect();
        return view('attendance.student-index', compact('student', 'attendance'));
    }

    public function parentIndex(Student $student)
    {
        $parent = auth()->user()->parent;

        if (! $parent || ! $parent->students()->whereKey($student->getKey())->exists()) {
            abort(403);
        }

        $attendance = Attendance::where('student_id', $student->id)->latest()->paginate(15);
        return view('attendance.parent-index', compact('student', 'attendance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'integer', 'exists:classrooms,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'records' => ['required', 'array', 'min:1'],
            'records.*.student_id' => ['required', 'integer', 'exists:students,id'],
            'records.*.status' => ['required', 'in:present,absent,late,excused'],
            'records.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $teacher = auth()->user()->teacher;

        foreach ($validated['records'] as $record) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $record['student_id'],
                    'date' => $validated['date'],
                    'classroom_id' => $validated['classroom_id'],
                ],
                [
                    'subject_id' => $validated['subject_id'],
                    'teacher_id' => $teacher?->id,
                    'status' => $record['status'],
                    'notes' => $record['notes'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Attendance recorded successfully.');
    }

    public function getStudents(Request $request)
    {
        $request->validate(['classroom_id' => 'required|exists:classrooms,id']);

        $classroom = Classroom::findOrFail($request->classroom_id);
        $this->authorizeTenant($classroom);
        $students = $classroom->enrollments()->where('status', 'active')->with('student.user')->get()->pluck('student');

        return response()->json($students);
    }
}