<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\PlatformAuditLog;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SchoolController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function index()
    {
        $schools = School::withCount('users')->latest()->paginate(20);
        return view('platform.schools.index', compact('schools'));
    }

    public function show(School $school)
    {
        $this->authorizeTenant($school);
        $school->load(['users','subscriptions.plan','invoices','paymentTransactions']);
        return view('platform.schools.show', compact('school'));
    }

    public function approve(Request $request, School $school)
    {
        $school->update(['status' => 'active', 'approved_at' => now(), 'suspended_at' => null, 'suspension_reason' => null]);
        PlatformAuditLog::create(['actor_id'=>$request->user()->id,'school_id'=>$school->id,'action'=>'school.approved','target_type'=>School::class,'target_id'=>$school->id,'ip_address'=>$request->ip()]);
        return back()->with('success', 'School approved.');
    }

    public function suspend(Request $request, School $school)
    {
        $request->validate(['reason'=>'nullable|string|max:1000']);
        $school->update(['status' => 'suspended', 'suspended_at' => now(), 'suspension_reason' => $request->reason]);
        PlatformAuditLog::create(['actor_id'=>$request->user()->id,'school_id'=>$school->id,'action'=>'school.suspended','target_type'=>School::class,'target_id'=>$school->id,'meta'=>['reason'=>$request->reason],'ip_address'=>$request->ip()]);
        return back()->with('success', 'School suspended.');
    }

    public function reactivate(Request $request, School $school)
    {
        $school->update(['status' => 'active', 'suspended_at' => null, 'suspension_reason' => null]);
        PlatformAuditLog::create(['actor_id'=>$request->user()->id,'school_id'=>$school->id,'action'=>'school.reactivated','target_type'=>School::class,'target_id'=>$school->id,'ip_address'=>$request->ip()]);
        return back()->with('success', 'School reactivated.');
    }
}

