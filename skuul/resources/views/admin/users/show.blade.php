<x-app-layout>
    <x-slot name="title">User Details</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $user->name }}</h1>
        <p class="text-gray-600 dark:text-slate-100">User account details</p>
    </div>

    <div class="card max-w-2xl">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Email</dt>
                <dd class="mt-1">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Role</dt>
                <dd class="mt-1">{{ $user->roles->pluck('name')->join(', ') ?: ucfirst($user->role_type) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Phone</dt>
                <dd class="mt-1">{{ $user->phone ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Status</dt>
                <dd class="mt-1">{{ $user->is_active ? 'Active' : 'Inactive' }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn-primary">Edit User</a>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Back to Users</a>
        </div>
    </div>
</x-app-layout>
