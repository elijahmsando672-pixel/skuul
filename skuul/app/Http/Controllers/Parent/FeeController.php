<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\FeeRecord;
use App\Models\Student;

class FeeController extends Controller
{
    public function index()
    {
        $parentId = auth()->user()?->parent?->id;

        $children = $parentId
            ? Student::with('user')->where('parent_id', $parentId)->orderBy('id')->get()
            : collect();

        $feeRecords = $parentId
            ? FeeRecord::with(['student.user', 'feeStructure.academicYear'])
                ->whereHas('student', fn ($query) => $query->where('parent_id', $parentId))
                ->orderBy('due_date')
                ->orderByDesc('id')
                ->get()
            : collect();

        return view('parent.fees.index', compact('children', 'feeRecords'));
    }
}
