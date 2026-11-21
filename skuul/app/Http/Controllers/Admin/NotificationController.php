<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\BookIssue;
use App\Models\Classroom;
use App\Models\ExamResult;
use App\Models\FeeRecord;
use App\Models\Student;
use App\Models\User;
use App\Notifications\EnrollmentConfirmation;
use App\Notifications\ExamResultPublished;
use App\Notifications\FeePaymentReminder;
use App\Notifications\LibraryBookDue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    public function index()
    {
        return view('admin.notifications.index', [
            'users' => User::where('is_active', true)->orderBy('name')->get(),
            'students' => Student::with('user')->orderBy('id')->get(),
            'classrooms' => Classroom::orderBy('name')->get(),
            'academicYears' => AcademicYear::orderByDesc('start_date')->get(),
            'feeRecords' => FeeRecord::with('student.user')->orderByDesc('id')->limit(100)->get(),
            'examResults' => ExamResult::with('exam', 'student.user')->orderByDesc('id')->limit(100)->get(),
            'bookIssues' => BookIssue::with('book')->orderByDesc('id')->limit(100)->get(),
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['enrollment', 'fee', 'exam', 'library'])],
            'user_id' => 'required|exists:users,id',
            'student_id' => 'required_if:type,enrollment|nullable|exists:students,id',
            'classroom_id' => 'required_if:type,enrollment|nullable|exists:classrooms,id',
            'academic_year_id' => 'required_if:type,enrollment|nullable|exists:academic_years,id',
            'fee_record_id' => 'required_if:type,fee|nullable|exists:fee_records,id',
            'exam_result_id' => 'required_if:type,exam|nullable|exists:exam_results,id',
            'book_issue_id' => 'required_if:type,library|nullable|exists:book_issues,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $notification = $this->buildNotification($validated);

        if (! $notification) {
            return back()->with('error', 'The selected notification details are incomplete.');
        }

        $user->notify($notification);

        return back()->with('success', 'Notification sent successfully.');
    }

    private function buildNotification(array $validated): ?object
    {
        return match ($validated['type']) {
            'enrollment' => new EnrollmentConfirmation(
                Student::findOrFail($validated['student_id']),
                Classroom::findOrFail($validated['classroom_id']),
                AcademicYear::findOrFail($validated['academic_year_id']),
            ),
            'fee' => new FeePaymentReminder(FeeRecord::findOrFail($validated['fee_record_id'])),
            'exam' => new ExamResultPublished(ExamResult::with('exam')->findOrFail($validated['exam_result_id'])->exam, ExamResult::findOrFail($validated['exam_result_id'])),
            'library' => new LibraryBookDue(BookIssue::findOrFail($validated['book_issue_id'])),
            default => null,
        };
    }
}
