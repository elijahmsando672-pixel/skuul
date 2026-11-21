<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $school = TenantContext::school();
        if (!$school || $request->user()?->is_platform_admin) {
            return $next($request);
        }

        $sub = $school->currentSubscription()->with('plan')->first();
        $allowed = ['trial', 'active', 'past_due'];
        if (!$sub || !in_array($sub->status, $allowed, true)) {
            if ($request->routeIs('*.billing.*') || $request->is('platform/*')) {
                return $next($request);
            }
            return redirect()->route('dashboard')->with('error', 'School subscription is inactive. Please update billing.');
        }

        return $next($request);
    }
}

