<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;

class DashboardController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_classrooms' => Classroom::count(),
            'total_subjects' => Subject::count(),
        ];

        $announcements = Announcement::where('is_published', true)->latest()->limit(5)->get();

        return view('dashboard', compact('stats', 'announcements'));
    }
}