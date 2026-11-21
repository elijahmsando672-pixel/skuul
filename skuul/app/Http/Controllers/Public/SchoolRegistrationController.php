<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolRegistrationController extends Controller
{
    public function create()
    {
        $plans = Plan::where('is_active', true)->orderBy('monthly_price')->get();
        return view('marketing.register-school', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_name'=>'required|string|max:255',
            'school_type'=>'nullable|string|max:255',
            'registration_number'=>'nullable|string|max:255',
            'country'=>'required|string|max:255',
            'county'=>'nullable|string|max:255',
            'address'=>'nullable|string|max:255',
            'contact_email'=>'required|email|max:255',
            'phone'=>'nullable|string|max:50',
            'admin_name'=>'required|string|max:255',
            'admin_email'=>'required|email|max:255|unique:users,email',
            'admin_password'=>'required|string|min:8|confirmed',
            'plan_id'=>'required|exists:plans,id',
            'billing_cycle'=>'required|in:monthly,annual',
        ]);

        DB::transaction(function () use ($validated) {
            $school = School::create([
                'name'=>$validated['school_name'],
                'slug'=>Str::slug($validated['school_name']).'-'.Str::lower(Str::random(5)),
                'registration_number'=>$validated['registration_number'] ?? null,
                'school_type'=>$validated['school_type'] ?? null,
                'country'=>$validated['country'],
                'county'=>$validated['county'] ?? null,
                'address'=>$validated['address'] ?? null,
                'contact_email'=>$validated['contact_email'],
                'phone'=>$validated['phone'] ?? null,
                'status'=>'pending',
                'settings'=>['onboarding'=>['registered'=>true,'email_verified'=>false,'approved'=>false]],
            ]);

            $admin = User::create([
                'current_school_id'=>$school->id,
                'name'=>$validated['admin_name'],
                'email'=>$validated['admin_email'],
                'password'=>Hash::make($validated['admin_password']),
                'role_type'=>'admin',
                'is_active'=>true,
                'is_platform_admin'=>false,
            ]);
            $admin->assignRole('admin');
            $school->users()->attach($admin->id, ['role'=>'admin','is_owner'=>true,'is_active'=>true,'joined_at'=>now()]);

            $plan = Plan::findOrFail($validated['plan_id']);
            Subscription::create([
                'school_id'=>$school->id,
                'plan_id'=>$plan->id,
                'billing_cycle'=>$validated['billing_cycle'],
                'status'=>'trial',
                'trial_ends_at'=>now()->addDays($plan->trial_days ?? 14),
                'starts_at'=>now(),
            ]);
        });

        return redirect()->route('login')->with('success', 'School registration submitted. Await platform approval and verify your email.');
    }
}
