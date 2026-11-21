<x-app-layout>
    <x-slot name="title">Edit User</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Edit User</h2>
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="form-label">Name</label>
                    <input id="name" type="text" name="name" class="input-field" value="{{ old('name', $user->name) }}" required>
                </div>
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" class="input-field" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="form-label">New Password</label>
                        <input id="password" type="password" name="password" class="input-field" minlength="8">
                    </div>
                    <div>
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="input-field" minlength="8">
                    </div>
                    <div>
                        <label for="role_type" class="form-label">Role</label>
                        <select id="role_type" name="role_type" class="input-field" required>
                            @forelse($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role_type', $user->role_type) === $role->name)>{{ ucfirst($role->name) }}</option>
                            @empty
                                <option value="" disabled>No roles available</option>
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label for="phone" class="form-label">Phone</label>
                        <input id="phone" type="text" name="phone" class="input-field" value="{{ old('phone', $user->phone) }}">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $user->is_active))>
                    <label for="is_active" class="form-label mb-0">Active</label>
                </div>
                <button type="submit" class="btn-primary w-full">Update User</button>
            </form>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary w-full">Back to Users</a>
    </div>
</x-app-layout>
