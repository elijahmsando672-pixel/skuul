<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\PlatformAuditLog;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_schools' => School::count(),
            'active_schools' => School::where('status', 'active')->count(),
            'pending_schools' => School::where('status', 'pending')->count(),
            'suspended_schools' => School::where('status', 'suspended')->count(),
            'total_students' => User::where('role_type', 'student')->count(),
            'total_teachers' => User::where('role_type', 'teacher')->count(),
            'active_subscriptions' => Subscription::whereIn('status', ['active', 'trial'])->count(),
            'expired_subscriptions' => Subscription::whereIn('status', ['expired', 'cancelled'])->count(),
            'mrr' => Subscription::where('status', 'active')->where('billing_cycle', 'monthly')->join('plans', 'plans.id', '=', 'subscriptions.plan_id')->sum('plans.monthly_price'),
            'arr' => Subscription::where('status', 'active')->where('billing_cycle', 'annual')->join('plans', 'plans.id', '=', 'subscriptions.plan_id')->sum('plans.annual_price'),
        ];
        $recentSchools = School::latest()->limit(10)->get();
        $recentPayments = PaymentTransaction::with('school')->latest()->limit(10)->get();
        $recentAudits = PlatformAuditLog::with(['actor','school'])->latest()->limit(15)->get();
        return view('platform.dashboard', compact('stats','recentSchools','recentPayments','recentAudits'));
    }
}
