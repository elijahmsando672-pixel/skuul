<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $school = \App\Support\Tenancy\TenantContext::school();

        $user = User::create([
            'current_school_id' => $school?->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_type' => 'student',
            'phone' => $request->phone,
            'is_active' => true,
            'is_platform_admin' => false,
        ]);

        $user->assignRole('student');
        if ($school) {
            $school->users()->syncWithoutDetaching([$user->id => ['role' => 'student', 'is_active' => true, 'joined_at' => now()]]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}