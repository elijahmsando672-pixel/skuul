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
            if ($request->has('school_id')) {
                $school = School::find($request->integer('school_id'));
                if ($school) TenantContext::set($school);
            }
            return $next($request);
        }

        $school = null;
        $host = $request->getHost();
        $baseHost = parse_url(config('app.url'), PHP_URL_HOST);
        if ($baseHost && $host !== $baseHost && $host !== 'localhost' && $host !== '127.0.0.1' && str_ends_with($host, '.'.$baseHost)) {
            $slug = substr($host, 0, -strlen('.'.$baseHost));
            if ($slug !== '' && $slug !== 'www') {
                $school = School::where('slug', $slug)->first();
            }
        }

        if (! $school && $user) {
            $query = $user->schools()->wherePivot('is_active', true);
            $school = ($user->current_school_id ? (clone $query)->where('schools.id', $user->current_school_id)->first() : null)
                ?? $query->first();
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

