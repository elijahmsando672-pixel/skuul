<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        $subjects = $teacher ? $teacher->subjects()->with('classroom')->get() : collect();
        $recentAttendance = Attendance::where('teacher_id', $teacher?->id)->latest()->limit(5)->get();
        $recentGrades = $teacher
            ? Grade::where(function (Builder $query) use ($teacher) {
                $query->whereHas('subject', fn (Builder $q) => $q->where('teacher_id', $teacher->id))
                    ->orWhereHas('classroom', fn (Builder $q) => $q->where('teacher_id', $teacher->id));
            })->with('student.user', 'subject')->latest()->limit(5)->get()
            : collect();

        return view('teacher.dashboard', compact('teacher', 'subjects', 'recentAttendance', 'recentGrades'));
    }
}