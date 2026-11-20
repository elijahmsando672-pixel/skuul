<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::with('student.user', 'subject')->latest()->paginate(15);
        return view('grades.index', compact('grades'));
    }

    public function studentIndex()
    {
        $student = auth()->user()->student;
        $grades = $student ? Grade::where('student_id', $student->id)->with('subject')->latest()->paginate(15) : collect();
        return view('grades.student-index', compact('student', 'grades'));
    }

    public function parentIndex(Student $student)
    {
        $grades = Grade::where('student_id', $student->id)->with('subject')->latest()->paginate(15);
        return view('grades.parent-index', compact('student', 'grades'));
    }

    public function create()
    {
        $students = Student::with('user')->get();
        $subjects = Subject::with('classroom')->get();
        return view('grades.create', compact('students', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type' => 'required|in:quiz,assignment,midterm,final,project',
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'weight' => 'nullable|numeric|min:0',
            'date_recorded' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $subject = Subject::find($validated['subject_id']);
        $validated['classroom_id'] = $subject->classroom_id;
        $validated['weight'] = $validated['weight'] ?? 1.00;

        Grade::create($validated);

        return redirect()->route('grades.index')->with('success', 'Grade recorded successfully.');
    }

    public function show(Grade $grade)
    {
        return view('grades.show', compact('grade'));
    }

    public function edit(Grade $grade)
    {
        $students = Student::with('user')->get();
        $subjects = Subject::with('classroom')->get();
        return view('grades.edit', compact('grade', 'students', 'subjects'));
    }

    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type' => 'required|in:quiz,assignment,midterm,final,project',
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'weight' => 'nullable|numeric|min:0',
            'date_recorded' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $subject = Subject::find($validated['subject_id']);
        $validated['classroom_id'] = $subject->classroom_id;
        $validated['weight'] = $validated['weight'] ?? 1.00;

        $grade->update($validated);

        return redirect()->route('grades.index')->with('success', 'Grade updated successfully.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return back()->with('success', 'Grade deleted successfully.');
    }
}