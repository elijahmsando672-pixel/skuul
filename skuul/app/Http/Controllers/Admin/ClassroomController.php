<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::with('academicYear', 'teacher')->paginate(15);
        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        $academicYears = AcademicYear::all();
        $teachers = Teacher::with('user')->get();
        return view('admin.classrooms.create', compact('academicYears', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string',
            'section' => 'nullable|string|max:10',
            'academic_year_id' => 'required|exists:academic_years,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'room_number' => 'nullable|string|max:20',
            'capacity' => 'nullable|integer|min:1',
        ]);

        Classroom::create($validated);

        return redirect()->route('admin.classrooms.index')->with('success', 'Classroom created successfully.');
    }

    public function show(Classroom $classroom)
    {
        return view('admin.classrooms.show', compact('classroom'));
    }

    public function edit(Classroom $classroom)
    {
        $academicYears = AcademicYear::all();
        $teachers = Teacher::with('user')->get();
        return view('admin.classrooms.edit', compact('classroom', 'academicYears', 'teachers'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string',
            'section' => 'nullable|string|max:10',
            'academic_year_id' => 'required|exists:academic_years,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'room_number' => 'nullable|string|max:20',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $classroom->update($validated);

        return redirect()->route('admin.classrooms.index')->with('success', 'Classroom updated successfully.');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return redirect()->route('admin.classrooms.index')->with('success', 'Classroom deleted successfully.');
    }
}