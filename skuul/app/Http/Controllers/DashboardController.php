<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];

        if ($user->is_platform_admin) {
            return redirect()->route('platform.dashboard');
        }

        if ($user->hasRole('admin')) {
            $stats = [
                'total_students' => Student::count(),
                'total_teachers' => Teacher::count(),
                'total_classrooms' => Classroom::count(),
                'total_subjects' => Subject::count(),
            ];
        } elseif ($user->hasRole('teacher')) {
            $teacher = $user->teacher;
            $stats = [
                'my_subjects' => $teacher ? $teacher->subjects()->count() : 0,
                'total_students' => Student::count(),
                'recent_attendance' => Attendance::where('teacher_id', $teacher?->id)->count(),
            ];
        } elseif ($user->hasRole('student')) {
            $student = $user->student;
            $stats = [
                'my_attendance' => $student ? Attendance::where('student_id', $student->id)->count() : 0,
                'my_grades' => $student ? Grade::where('student_id', $student->id)->count() : 0,
            ];
        } elseif ($user->hasRole('parent')) {
            $parent = $user->parent;
            $stats = [
                'my_children' => $parent ? $parent->students()->count() : 0,
            ];
        }

        $announcements = Announcement::where('is_published', true)
            ->where(function ($q) {
                $q->where('target_audience', 'all')
                  ->orWhereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'announcements'));
    }
}