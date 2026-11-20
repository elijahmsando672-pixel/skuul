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
    public function teacherIndex()
    {
        $teacher = auth()->user()->teacher;
        $subjects = $teacher ? $teacher->subjects()->with('classroom')->get() : collect();
        return view('attendance.teacher-index', compact('teacher', 'subjects'));
    }

    public function studentIndex()
    {
        $student = auth()->user()->student;
        $attendance = $student ? Attendance::where('student_id', $student->id)->latest()->paginate(15) : collect();
        return view('attendance.student-index', compact('student', 'attendance'));
    }

    public function parentIndex(Student $student)
    {
        $attendance = Attendance::where('student_id', $student->id)->latest()->paginate(15);
        return view('attendance.parent-index', compact('student', 'attendance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'records' => 'required|array',
            'records.*.student_id' => 'required|exists:students,id',
            'records.*.status' => 'required|in:present,absent,late,excused',
            'records.*.notes' => 'nullable|string',
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

        $classroom = Classroom::find($request->classroom_id);
        $students = $classroom->enrollments()->where('status', 'active')->with('student.user')->get()->pluck('student');

        return response()->json($students);
    }
}