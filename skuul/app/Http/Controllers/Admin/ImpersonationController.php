<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function store(Request $request, User $user)
    {
        $admin = $request->user();

        abort_unless($admin && $admin->hasRole('admin'), 403);
        $this->authorizeTenant($user);
        abort_if($user->id === $admin->id, 403);
        abort_unless($user->is_active, 403);
        abort_unless(in_array($user->role_type, ['student', 'teacher', 'parent'], true), 403);
        abort_unless($user->hasRole($user->role_type), 403);

        $request->session()->put('impersonator_id', $admin->id);
        $request->session()->put('impersonator_name', $admin->name);

        Auth::login($user);

        $request->session()->migrate(true);

        return redirect()->route('dashboard')->with('success', 'You are now viewing SKUUL as '.$user->name.'.');
    }

    public function destroy(Request $request)
    {
        $impersonatorId = $request->session()->get('impersonator_id');
        $admin = $impersonatorId ? User::query()->where('current_school_id', $this->tenantId())->find($impersonatorId) : null;

        abort_unless($admin && $admin->hasRole('admin') && $admin->is_active, 403);

        Auth::login($admin);

        $request->session()->forget(['impersonator_id', 'impersonator_name']);
        $request->session()->migrate(true);

        return redirect()->route('admin.dashboard')->with('success', 'Returned to your admin account.');
    }
}



