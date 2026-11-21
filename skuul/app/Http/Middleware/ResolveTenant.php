<?php

namespace App\Http\Middleware;

use App\Models\School;
use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        TenantContext::clear();
        $user = $request->user();

        if ($user && $user->is_platform_admin) {
            // Platform admins must not inherit tenant data access implicitly.
            return $next($request);
        }

        $school = null;
        $host = $request->getHost();
        $baseHost = parse_url(config('app.url'), PHP_URL_HOST);
        $hostSchool = null;
        if ($baseHost && $host !== $baseHost && $host !== 'localhost' && $host !== '127.0.0.1' && str_ends_with($host, '.'.$baseHost)) {
            $slug = substr($host, 0, -strlen('.'.$baseHost));
            if ($slug !== '' && $slug !== 'www') {
                $hostSchool = School::where('slug', $slug)->first();
            }
        }

        if ($user) {
            $membershipQuery = $user->schools()->wherePivot('is_active', true)->where('schools.status', '!=', 'rejected');
            $hostSchool = $hostSchool && $user->schools()->wherePivot('is_active', true)->whereKey($hostSchool->id)->exists() ? $hostSchool : null;
            $currentSchool = $user->current_school_id
                ? (clone $membershipQuery)->where('schools.id', $user->current_school_id)->first()
                : null;

            $school = $hostSchool ?? $currentSchool ?? $membershipQuery->first();

            if ($hostSchool && $user->current_school_id !== $hostSchool->id) {
                $user->forceFill(['current_school_id' => $hostSchool->id])->saveQuietly();
            }
        } else {
            $school = $hostSchool;
        }

        if ($school) {
            if ($school->status === 'suspended' && ! $request->routeIs('platform.*')) {
                abort(403, 'This school account is suspended.');
            }
            TenantContext::set($school);
        }

        return $next($request);
    }
}

