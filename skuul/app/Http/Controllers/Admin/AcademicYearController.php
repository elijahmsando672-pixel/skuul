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

    public function show(AcademicYear $year)
    {
        return view('admin.academic-years.show', compact('year'));
    }

    public function edit(AcademicYear $year)
    {
        return view('admin.academic-years.edit', compact('year'));
    }

    public function update(Request $request, AcademicYear $year)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $year->update($validated);

        return redirect()->route('admin.academic-years.index')->with('success', 'Academic year updated successfully.');
    }

    public function destroy(AcademicYear $year)
    {
        $year->delete();
        return redirect()->route('admin.academic-years.index')->with('success', 'Academic year deleted successfully.');
    }
}