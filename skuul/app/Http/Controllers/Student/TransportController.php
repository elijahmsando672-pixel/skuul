<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TransportAssignment;

class TransportController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function index()
    {
        $student = auth()->user()?->student;

        $assignment = $student
            ? TransportAssignment::query()
                ->with(['assignable.user', 'transportRoute', 'vehicle', 'academicYear'])
                ->where('assignable_type', 'student')
                ->where('assignable_id', $student->id)
                ->where('is_active', true)
                ->latest()
                ->first()
            : null;

        $assignments = $assignment ? collect([$assignment]) : collect();

        return view('student.transport.index', compact('student', 'assignment', 'assignments'));
    }
}
