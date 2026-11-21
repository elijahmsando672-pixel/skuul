<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use App\Models\Subject;
use App\Notifications\ExamResultPublished;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('academicYear', 'subject')->latest('exam_date')->paginate(15);

        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $subjects = Subject::with('classroom')->get();

        return view('admin.exams.create', compact('academicYears', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateExam($request);

        Exam::create($validated);

        return redirect()->route('admin.exams.index')->with('success', 'Exam created successfully.');
    }

    public function show(Exam $exam)
    {
        return redirect()->route('admin.exams.results', $exam);
    }

    public function edit(Exam $exam)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $subjects = Subject::with('classroom')->get();

        return view('admin.exams.edit', compact('exam', 'academicYears', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $this->validateExam($request);

        $exam->update($validated);

        return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
    }

    public function results(Exam $exam)
    {
        $exam->load('academicYear', 'subject.classroom');

        $students = Student::with('user')
            ->whereHas('enrollments', function ($query) use ($exam) {
                $query->where('classroom_id', $exam->subject?->classroom_id)
                    ->where('academic_year_id', $exam->academic_year_id);
            })
            ->get();

        if ($students->isEmpty()) {
            $students = Student::with('user')->get();
        }

        $results = $exam->results()->with('student.user')->get()->keyBy('student_id');

        return view('admin.exams.results', compact('exam', 'students', 'results'));
    }

    public function resultsStore(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'results' => 'required|array',
            'results.*.student_id' => 'required|exists:students,id',
            'results.*.marks_obtained' => 'required|numeric|min:0',
            'results.*.remarks' => 'nullable|string|max:1000',
        ]);

        foreach ($validated['results'] as $result) {
            $percentage = $exam->total_marks > 0
                ? ((float) $result['marks_obtained'] / (float) $exam->total_marks) * 100
                : 0.0;

            ExamResult::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'student_id' => $result['student_id'],
                ],
                [
                    'marks_obtained' => $result['marks_obtained'],
                    'grade' => $this->gradeFor($percentage),
                    'remarks' => $result['remarks'] ?? null,
                ]
            );
        }

        return redirect()->route('admin.exams.results', $exam)->with('success', 'Exam results saved successfully.');
    }

    public function publish(Exam $exam)
    {
        $exam->update(['is_published' => ! $exam->is_published]);

        if ($exam->is_published) {
            foreach ($exam->results()->with('student.user')->get() as $result) {
                $result->student?->user?->notify(new ExamResultPublished($exam, $result));
            }
        }

        $message = $exam->is_published ? 'Exam published successfully.' : 'Exam unpublished successfully.';

        return redirect()->route('admin.exams.index')->with('success', $message);
    }

    private function validateExam(Request $request): array
    {
        return $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'exam_type' => 'required|string|max:100',
            'exam_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    $start = $request->input('start_time');

                    if ($start && $value <= $start) {
                        $fail('The end time must be after the start time.');
                    }
                },
            ],
            'total_marks' => 'required|numeric|min:0.01',
            'is_published' => 'boolean',
        ]);
    }

    private function gradeFor(float $percentage): string
    {
        if ($percentage >= 90) {
            return 'A';
        }

        if ($percentage >= 75) {
            return 'B';
        }

        if ($percentage >= 60) {
            return 'C';
        }

        if ($percentage >= 50) {
            return 'D';
        }

        return 'F';
    }
}
