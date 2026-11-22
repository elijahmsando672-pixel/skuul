<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function index()
    {
        $plans = Plan::orderBy('monthly_price')->get();
        return view('platform.plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'description'=>'nullable|string',
            'monthly_price'=>'required|numeric|min:0',
            'annual_price'=>'required|numeric|min:0',
            'currency'=>'required|string|max:10',
            'student_limit'=>'nullable|integer|min:0',
            'staff_limit'=>'nullable|integer|min:0',
            'storage_limit_mb'=>'nullable|integer|min:0',
            'trial_days'=>'required|integer|min:0|max:365',
            'is_custom'=>'nullable|boolean',
            'is_active'=>'nullable|boolean',
        ]);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(4));
        $data['is_custom'] = $request->boolean('is_custom');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['features'] = [];
        $data['enabled_modules'] = [];
        Plan::create($data);
        return back()->with('success', 'Plan created.');
    }

    public function update(Request $request, Plan $plan)
    {
        $this->authorizeTenant($plan);
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'description'=>'nullable|string',
            'monthly_price'=>'required|numeric|min:0',
            'annual_price'=>'required|numeric|min:0',
            'currency'=>'required|string|max:10',
            'student_limit'=>'nullable|integer|min:0',
            'staff_limit'=>'nullable|integer|min:0',
            'storage_limit_mb'=>'nullable|integer|min:0',
            'trial_days'=>'required|integer|min:0|max:365',
            'is_custom'=>'nullable|boolean',
            'is_active'=>'nullable|boolean',
        ]);
        $data['is_custom'] = $request->boolean('is_custom');
        $data['is_active'] = $request->boolean('is_active');
        $plan->update($data);
        return back()->with('success', 'Plan updated.');
    }
}

