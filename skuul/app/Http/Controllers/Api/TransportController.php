<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransportAssignment;
use App\Models\TransportRoute;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function routes(Request $request): JsonResponse
    {
        $query = TransportRoute::with('vehicles');

        if (! $this->isAdmin($request)) {
            $query->where('is_active', true);
        }

        return response()->json($query->orderBy('name')->paginate($this->perPage($request)));
    }

    public function vehicles(Request $request): JsonResponse
    {
        $query = Vehicle::with('routes');

        if (! $this->isAdmin($request)) {
            $query->where('is_active', true);
        }

        return response()->json($query->orderBy('registration_number')->paginate($this->perPage($request)));
    }

    public function assignments(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = TransportAssignment::with(['transportRoute', 'vehicle', 'academicYear', 'assignable']);

        if (! $this->isAdmin($request)) {
            if ($user->hasRole('student')) {
                $query->where('assignable_type', 'student')->where('assignable_id', $user->student?->id ?? 0);
            } elseif ($user->hasRole('parent')) {
                $query->where('assignable_type', 'student')->whereIn('assignable_id', $this->childIds($user) ?: [0]);
            } elseif ($user->hasRole('teacher')) {
                $query->where('assignable_type', 'teacher')->where('assignable_id', $user->teacher?->id ?? 0);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return response()->json($query->orderByDesc('id')->paginate($this->perPage($request)));
    }

    private function isAdmin(Request $request): bool
    {
        return (bool) $request->user()?->hasRole('admin');
    }

    private function childIds(User $user): array
    {
        $parent = $user->parent;

        return $parent ? $parent->students()->pluck('id')->all() : [];
    }

    private function perPage(Request $request): int
    {
        return max(1, min(100, (int) $request->query('per_page', 15)));
    }
}
