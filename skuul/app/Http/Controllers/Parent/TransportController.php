<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\TransportAssignment;

class TransportController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function index()
    {
        $parent = auth()->user()?->parent;
        $children = $parent ? $parent->students()->with('user')->orderBy('id')->get() : collect();

        $assignments = collect();

        if ($children->isNotEmpty()) {
            $assignments = TransportAssignment::query()
                ->with(['assignable.user', 'transportRoute', 'vehicle', 'academicYear'])
                ->where('assignable_type', 'student')
                ->whereIn('assignable_id', $children->pluck('id'))
                ->where('is_active', true)
                ->latest()
                ->get();
        }

        return view('parent.transport.index', compact('parent', 'children', 'assignments'));
    }
}
