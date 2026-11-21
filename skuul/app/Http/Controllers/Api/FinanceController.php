<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
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

        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
                Rule::unique('fee_records')->where(function ($query) use ($request) {
                    return $query->where('fee_structure_id', $request->input('fee_structure_id'));
                }),
            ],
            'fee_structure_id' => ['required', 'integer', 'exists:fee_structures,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'status' => ['nullable', 'string', 'max:50'],
            'paid_at' => ['nullable', 'date'],
        ]);

        $feeRecord = FeeRecord::create($validated);

        return response()->json($feeRecord->load(['student.user', 'feeStructure']), 201);
    }

    public function updateFeeRecord(Request $request, FeeRecord $feeRecord): JsonResponse
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'amount_paid' => ['sometimes', 'required', 'numeric', 'min:0'],
            'due_date' => ['sometimes', 'required', 'date'],
            'status' => ['sometimes', 'required', 'string', 'max:50'],
            'paid_at' => ['sometimes', 'nullable', 'date'],
        ]);

        $feeRecord->update($validated);

        return response()->json($feeRecord->load(['student.user', 'feeStructure']));
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
