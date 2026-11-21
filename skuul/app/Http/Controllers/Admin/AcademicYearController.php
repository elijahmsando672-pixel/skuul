<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.academic-years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        AcademicYear::create($validated);

        return redirect()->route('admin.academic-years.index')->with('success', 'Academic year created successfully.');
    }

    public function show(AcademicYear $academicYear)
    {
        return view('admin.academic-years.show', ['year' => $academicYear]);
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', ['year' => $academicYear]);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $academicYear->update($validated);

        return redirect()->route('admin.academic-years.index')->with('success', 'Academic year updated successfully.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->route('admin.academic-years.index')->with('success', 'Academic year deleted successfully.');
    }
}