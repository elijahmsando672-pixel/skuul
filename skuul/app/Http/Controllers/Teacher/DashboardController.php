<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        $subjects = $teacher ? $teacher->subjects()->with('classroom')->get() : collect();
        $recentAttendance = Attendance::where('teacher_id', $teacher?->id)->latest()->limit(5)->get();
        $recentGrades = Grade::where('teacher_id', $teacher?->id)->latest()->limit(5)->get();

        return view('teacher.dashboard', compact('teacher', 'subjects', 'recentAttendance', 'recentGrades'));
    }
}