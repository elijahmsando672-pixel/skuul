<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;

class DashboardController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function index()
    {
        $parent = auth()->user()->parent;
        $children = $parent ? $parent->students()->with('user')->get() : collect();

        return view('parent.dashboard', compact('parent', 'children'));
    }
}