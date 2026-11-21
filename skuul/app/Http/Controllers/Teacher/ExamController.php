<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;

class ExamController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        $subjectIds = $teacher ? $teacher->subjects()->pluck('id') : collect();

        $exams = $subjectIds->isNotEmpty()
            ? Exam::with('academicYear', 'subject.classroom')
                ->whereIn('subject_id', $subjectIds)
                ->latest('exam_date')
                ->get()
            : collect();

        return view('teacher.exams.index', compact('teacher', 'exams'));
    }
}
