<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Notifications\FeePaymentReminder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeeController extends Controller
{
    public function index()
    {
        $feeStructures = FeeStructure::with('academicYear')
            ->withCount('feeRecords')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.fees.index', compact('feeStructures'));
    }

    public function create()
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('admin.fees.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'frequency' => ['required', Rule::in(['monthly', 'termly', 'annual'])],
            'due_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        FeeStructure::create($validated);

        return redirect()->route('admin.fees.index')->with('success', 'Fee structure created successfully.');
    }

    public function show(FeeStructure $fee)
    {
        $schoolId = \App\Support\Tenancy\TenantContext::schoolId();
        if ($schoolId && (int) $fee->school_id !== (int) $schoolId) abort(404);
        return view('admin.fees.show', compact('fee'));
    }

    public function edit(FeeStructure $fee)
    {
        $feeStructure = $fee;
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('admin.fees.edit', compact('feeStructure', 'academicYears'));
    }

    public function update(Request $request, FeeStructure $fee)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'frequency' => ['required', Rule::in(['monthly', 'termly', 'annual'])],
            'due_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $fee->update($validated);

        return redirect()->route('admin.fees.index')->with('success', 'Fee structure updated successfully.');
    }

    public function destroy(FeeStructure $fee)
    {
        $fee->delete();

        return redirect()->route('admin.fees.index')->with('success', 'Fee structure deleted successfully.');
    }

    public function records()
    {
        $feeRecords = FeeRecord::with(['student.user', 'feeStructure.academicYear'])
            ->orderBy('due_date')
            ->orderByDesc('id')
            ->paginate(15);

        $students = Student::with('user')->orderBy('id')->get();
        $feeStructures = FeeStructure::with('academicYear')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.fees.records', compact('feeRecords', 'students', 'feeStructures'));
    }

    public function recordStore(Request $request)
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'exists:students,id',
                Rule::unique('fee_records', 'student_id')->where(fn ($query) => $query->where('fee_structure_id', $request->integer('fee_structure_id'))),
            ],
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'amount' => 'required|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0|lte:amount',
            'due_date' => 'required|date',
            'status' => ['required', Rule::in(['pending', 'partial', 'paid', 'overdue'])],
        ]);

        $amount = (float) $validated['amount'];
        $amountPaid = (float) ($validated['amount_paid'] ?? 0);
        $status = $validated['status'];

        if ($amountPaid >= $amount) {
            $status = 'paid';
        } elseif ($amountPaid > 0 && $status === 'pending') {
            $status = 'partial';
        }

        FeeRecord::create([
            'student_id' => $validated['student_id'],
            'fee_structure_id' => $validated['fee_structure_id'],
            'amount' => $amount,
            'amount_paid' => $amountPaid,
            'due_date' => $validated['due_date'],
            'status' => $status,
            'paid_at' => $status === 'paid' ? now() : null,
        ]);

        return redirect()->route('admin.fees.records')->with('success', 'Fee record created successfully.');
    }

    public function recordStatus(Request $request, FeeRecord $feeRecord)
    {
        $validated = $request->validate([
            'amount_paid' => ['required', 'numeric', 'min:0', 'max:'.$feeRecord->amount],
            'status' => ['required', Rule::in(['pending', 'partial', 'paid', 'overdue'])],
        ]);

        $amount = (float) $feeRecord->amount;
        $amountPaid = (float) $validated['amount_paid'];

        if ($amountPaid >= $amount) {
            $status = 'paid';
            $paidAt = now();
        } elseif ($amountPaid > 0) {
            $status = 'partial';
            $paidAt = null;
        } else {
            $status = in_array($validated['status'], ['pending', 'overdue'], true) ? $validated['status'] : 'pending';
            $paidAt = null;
        }

        $feeRecord->update([
            'amount_paid' => $amountPaid,
            'status' => $status,
            'paid_at' => $paidAt,
        ]);

        if (in_array($status, ['pending', 'overdue'], true)) {
            $feeRecord->load('student.user');
            $feeRecord->student?->user?->notify(new FeePaymentReminder($feeRecord->fresh()));
        }

        return redirect()->route('admin.fees.records')->with('success', 'Fee payment updated successfully.');
    }
}
