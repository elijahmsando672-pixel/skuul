<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function index()
    {
        $teachers = Teacher::with('user')->paginate(15);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $users = User::where('role_type', 'teacher')->whereDoesntHave('teacher')->get();
        return view('admin.teachers.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'employee_id' => 'required|string|unique:teachers',
            'department' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'qualification' => 'nullable|string',
            'specialization' => 'nullable|string',
        ]);

        Teacher::create($validated);

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function show(Teacher $teacher)
    {
        $this->authorizeTenant($teacher);
        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $this->authorizeTenant($teacher);
        $users = User::where('role_type', 'teacher')->get();
        return view('admin.teachers.edit', compact('teacher', 'users'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $this->authorizeTenant($teacher);
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'employee_id' => 'required|string|unique:teachers,employee_id,' . $teacher->id,
            'department' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'qualification' => 'nullable|string',
            'specialization' => 'nullable|string',
        ]);

        $teacher->update($validated);

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        $this->authorizeTenant($teacher);
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }
}