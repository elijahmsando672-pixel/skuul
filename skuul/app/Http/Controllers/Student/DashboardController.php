<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Student;

class DashboardController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function index()
    {
        $student = auth()->user()->student;
        $attendance = $student ? Attendance::where('student_id', $student->id)->latest()->limit(10)->get() : collect();
        $grades = $student ? Grade::where('student_id', $student->id)->latest()->limit(10)->get() : collect();

        return view('student.dashboard', compact('student', 'attendance', 'grades'));
    }
}