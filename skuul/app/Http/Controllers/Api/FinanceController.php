<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
    private const STATUSES = ['pending', 'partial', 'paid', 'overdue'];

    public function feeStructures(Request $request): JsonResponse
    {
        $query = FeeStructure::with('academicYear');

        if (! $this->isAdmin($request)) {
            $query->where('is_active', true);
        }

        return response()->json($query->orderBy('name')->paginate($this->perPage($request)));
    }

    public function feeRecords(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = FeeRecord::with(['student.user', 'feeStructure']);

        if (! $this->isAdmin($request)) {
            if ($user->hasRole('parent')) {
                $query->whereIn('student_id', $this->childIds($user) ?: [0]);
            } elseif ($user->hasRole('student')) {
                $query->where('student_id', $user->student?->id ?? 0);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return response()->json($query->orderByDesc('due_date')->paginate($this->perPage($request)));
    }

    public function storeFeeRecord(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $validated = $this->validateFeeRecord($request);

        $amount = (float) $validated['amount'];
        $amountPaid = (float) ($validated['amount_paid'] ?? 0);
        $status = $validated['status'] ?? 'pending';

        if ($amountPaid >= $amount) {
            $status = 'paid';
        } elseif ($amountPaid > 0 && $status === 'pending') {
            $status = 'partial';
        }

        $feeRecord = FeeRecord::create([
            'student_id' => $validated['student_id'],
            'fee_structure_id' => $validated['fee_structure_id'],
            'amount' => $amount,
            'amount_paid' => $amountPaid,
            'due_date' => $validated['due_date'],
            'status' => $status,
            'paid_at' => $status === 'paid' ? now() : null,
        ]);

        return response()->json($feeRecord->load(['student.user', 'feeStructure']), 201);
    }

    public function showFeeRecord(Request $request, FeeRecord $feeRecord): JsonResponse
    {
        $user = $request->user();
        if (! $this->isAdmin($request)) {
            if ($user->hasRole('parent')) {
                abort_unless(in_array($feeRecord->student_id, $this->childIds($user)), 404);
            } elseif ($user->hasRole('student')) {
                abort_unless($feeRecord->student_id === ($user->student?->id ?? 0), 404);
            } else {
                abort(404);
            }
        }
        return response()->json($feeRecord->load(['student.user','feeStructure']));
    }

    public function updateFeeRecord(Request $request, FeeRecord $feeRecord): JsonResponse
    {
        $this->ensureAdmin($request);

        $validated = $this->validateFeeRecord($request, $feeRecord);

        $amount = array_key_exists('amount', $validated) ? (float) $validated['amount'] : (float) $feeRecord->amount;
        $amountPaid = array_key_exists('amount_paid', $validated) ? (float) $validated['amount_paid'] : (float) $feeRecord->amount_paid;
        $status = $validated['status'] ?? $feeRecord->status;

        if ($amountPaid >= $amount) {
            $status = 'paid';
            $paidAt = now();
        } elseif ($amountPaid > 0) {
            $status = 'partial';
            $paidAt = null;
        } else {
            $status = in_array($status, ['pending', 'overdue'], true) ? $status : 'pending';
            $paidAt = null;
        }

        $feeRecord->update([
            'amount' => $amount,
            'amount_paid' => $amountPaid,
            'due_date' => $validated['due_date'] ?? $feeRecord->due_date,
            'status' => $status,
            'paid_at' => $paidAt,
        ]);

        return response()->json($feeRecord->load(['student.user', 'feeStructure']));
    }

    private function validateFeeRecord(Request $request, ?FeeRecord $feeRecord = null): array
    {
        $updating = $feeRecord !== null;
        $rules = [
            'amount' => [$updating ? 'sometimes|required' : 'required', 'numeric', 'min:0'],
            'amount_paid' => [$updating ? 'sometimes|nullable' : 'nullable', 'numeric', 'min:0'],
            'due_date' => [$updating ? 'sometimes|required' : 'required', 'date'],
            'status' => [$updating ? 'sometimes|required' : 'nullable', Rule::in(self::STATUSES)],
        ];

        if (! $updating) {
            $rules['student_id'] = [
                'required',
                'integer',
                'exists:students,id',
                Rule::unique('fee_records')->where(fn ($query) => $query->where('fee_structure_id', $request->input('fee_structure_id'))),
            ];
            $rules['fee_structure_id'] = ['required', 'integer', 'exists:fee_structures,id'];
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->after(function ($validator) use ($request, $feeRecord) {
            $amount = (float) ($request->input('amount', $feeRecord?->amount ?? 0));
            $amountPaid = (float) ($request->input('amount_paid', $feeRecord?->amount_paid ?? 0));

            if ($amountPaid > $amount) {
                $validator->errors()->add('amount_paid', 'The amount paid may not be greater than amount.');
            }
        });

        return $validator->validate();
    }

    private function isAdmin(Request $request): bool
    {
        return (bool) $request->user()?->hasRole('admin');
    }

    private function ensureAdmin(Request $request): void
    {
        if (! $this->isAdmin($request)) {
            abort(403, 'Admin access required.');
        }
    }

    private function childIds(User $user): array
    {
        $parent = $user->parent;

        return $parent ? $parent->students()->pluck('id')->all() : [];
    }

    private function perPage(Request $request): int
    {
        return max(1, min(100, (int) $request->query('per_page', 15)));
    }
}
