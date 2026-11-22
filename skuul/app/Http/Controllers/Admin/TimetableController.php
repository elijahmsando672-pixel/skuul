<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TimetableController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    private const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public function index()
    {
        $timetables = Timetable::with('academicYear', 'classroom', 'subject', 'teacher.user')
            ->get()
            ->sortBy(function ($timetable) {
                return [array_search($timetable->day_of_week, self::DAYS), $timetable->start_time];
            })
            ->values();

        return view('admin.timetables.index', compact('timetables'));
    }

    public function create()
    {
        return view('admin.timetables.create', [
            'academicYears' => AcademicYear::orderByDesc('is_active')->get(),
            'classrooms' => Classroom::with('academicYear')->orderBy('name')->get(),
            'subjects' => Subject::with('classroom')->orderBy('name')->get(),
            'teachers' => Teacher::with('user')->get()->sortBy(fn ($teacher) => $teacher->user->name ?? ''),
            'days' => self::DAYS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTimetable($request);
        $error = $this->validateTimeRange($validated['start_time'], $validated['end_time']);

        if ($error) {
            return back()->withErrors(['end_time' => $error])->withInput();
        }

        Timetable::create($validated);

        return redirect()->route('admin.timetables.index')->with('success', 'Timetable entry created successfully.');
    }

    public function show(Timetable $timetable)
    {
        $this->authorizeTenant($timetable);
        $timetable->load('academicYear', 'classroom', 'subject', 'teacher.user');

        return view('admin.timetables.show', compact('timetable'));
    }

    public function edit(Timetable $timetable)
    {
        $this->authorizeTenant($timetable);
        return view('admin.timetables.edit', [
            'timetable' => $timetable,
            'academicYears' => AcademicYear::orderByDesc('is_active')->get(),
            'classrooms' => Classroom::with('academicYear')->orderBy('name')->get(),
            'subjects' => Subject::with('classroom')->orderBy('name')->get(),
            'teachers' => Teacher::with('user')->get()->sortBy(fn ($teacher) => $teacher->user->name ?? ''),
            'days' => self::DAYS,
        ]);
    }

    public function update(Request $request, Timetable $timetable)
    {
        $this->authorizeTenant($timetable);
        $validated = $this->validateTimetable($request, $timetable->id);
        $error = $this->validateTimeRange($validated['start_time'], $validated['end_time']);

        if ($error) {
            return back()->withErrors(['end_time' => $error])->withInput();
        }

        $timetable->update($validated);

        return redirect()->route('admin.timetables.index')->with('success', 'Timetable entry updated successfully.');
    }

    public function destroy(Timetable $timetable)
    {
        $this->authorizeTenant($timetable);
        $timetable->delete();

        return redirect()->route('admin.timetables.index')->with('success', 'Timetable entry deleted successfully.');
    }

    public function student()
    {
        $student = auth()->user()->student;
        $classroomIds = $student
            ? $student->enrollments()->pluck('classroom_id')->filter()->unique()->values()->all()
            : [];

        $timetables = $classroomIds !== []
            ? Timetable::with('classroom', 'subject', 'teacher.user')
                ->whereIn('classroom_id', $classroomIds)
                ->get()
                ->sortBy(function ($timetable) {
                    return [array_search($timetable->day_of_week, self::DAYS), $timetable->start_time];
                })
                ->values()
            : collect();

        return view('student.timetable.index', compact('timetables', 'student'));
    }

    public function teacher()
    {
        $teacher = auth()->user()->teacher;

        $timetables = $teacher
            ? Timetable::with('classroom', 'subject', 'academicYear')
                ->where('teacher_id', $teacher->id)
                ->get()
                ->sortBy(function ($timetable) {
                    return [array_search($timetable->day_of_week, self::DAYS), $timetable->start_time];
                })
                ->values()
            : collect();

        return view('teacher.timetable.index', compact('timetables', 'teacher'));
    }

    private function validateTimetable(Request $request, ?int $id = null): array
    {
        $unique = Rule::unique('timetables', 'start_time')->where(function ($query) use ($request) {
            return $query->where('classroom_id', $request->input('classroom_id'))
                ->where('day_of_week', $request->input('day_of_week'))
                ->where('start_time', $request->input('start_time'));
        });

        if ($id) {
            $unique->ignore($id);
        }

        return $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day_of_week' => ['required', Rule::in(self::DAYS)],
            'start_time' => ['required', 'date_format:H:i', $unique],
            'end_time' => 'required|date_format:H:i',
            'room' => 'nullable|string|max:100',
        ]);
    }

    private function validateTimeRange(string $start, string $end): ?string
    {
        return $end <= $start ? 'End time must be after start time.' : null;
    }
}


