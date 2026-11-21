<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use App\Notifications\EnrollmentConfirmation;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('user', 'parent', 'enrollments')->paginate(15);
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $users = User::where('role_type', 'student')->whereDoesntHave('student')->get();
        $parents = ParentModel::with('user')->get();
        return view('admin.students.create', compact('users', 'parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'student_id' => 'required|string|unique:students',
            'grade_level' => 'required|string',
            'enrollment_date' => 'nullable|date',
            'parent_id' => 'nullable|exists:parents,id',
            'emergency_contact' => 'nullable|string',
            'medical_info' => 'nullable|string',
        ]);

        Student::create($validated);

        return redirect()->route('admin.students.index')->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $this->authorizeSchoolRecord($student);
        return view('admin.students.show', compact('student'));
    }

    private function authorizeSchoolRecord($model): void
    {
        $schoolId = \App\Support\Tenancy\TenantContext::schoolId();
        if ($schoolId && (int) $model->school_id !== (int) $schoolId) {
            abort(404);
        }
    }

    public function edit(Student $student)
    {
        $users = User::where('role_type', 'student')->get();
        $parents = ParentModel::with('user')->get();
        return view('admin.students.edit', compact('student', 'users', 'parents'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'student_id' => 'required|string|unique:students,student_id,' . $student->id,
            'grade_level' => 'required|string',
            'enrollment_date' => 'nullable|date',
            'parent_id' => 'nullable|exists:parents,id',
            'emergency_contact' => 'nullable|string',
            'medical_info' => 'nullable|string',
        ]);

        $student->update($validated);

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
    }

    public function enroll(Request $request, Student $student)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_date' => 'required|date',
        ]);

        Enrollment::create([
            'student_id' => $student->id,
            'classroom_id' => $validated['classroom_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'enrollment_date' => $validated['enrollment_date'],
            'status' => 'active',
        ]);

        $student->load('user');
        $student->user?->notify(new EnrollmentConfirmation(
            $student,
            Classroom::findOrFail($validated['classroom_id']),
            AcademicYear::findOrFail($validated['academic_year_id']),
        ));

        return back()->with('success', 'Student enrolled successfully.');
    }
}