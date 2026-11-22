<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamResult;

class ExamController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function index()
    {
        $student = auth()->user()->student;

        $results = $student
            ? ExamResult::with('exam.subject', 'exam.academicYear')
                ->where('student_id', $student->id)
                ->whereHas('exam', function ($query) {
                    $query->where('is_published', true);
                })
                ->latest('id')
                ->get()
            : collect();

        return view('student.exams.index', compact('student', 'results'));
    }
}
