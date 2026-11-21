<x-app-layout>
    <x-slot name="title">Users</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Users</h1>
            <p class="text-gray-600">Manage system users and roles</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2"><path d="M12 4v16m8-8H4"/></svg>
            Add User
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Name</th>
                        <th class="table-header">Email</th>
                        <th class="table-header">Role</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr>
                            <td class="table-cell">{{ $user->name }}</td>
                            <td class="table-cell">{{ $user->email }}</td>
                            <td class="table-cell">
                                <span class="status-badge {{ $user->role_type === 'admin' ? 'bg-purple-100 text-purple-800' : ($user->role_type === 'teacher' ? 'bg-blue-100 text-blue-800' : ($user->role_type === 'student' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800')) }}">
                                    {{ ucfirst($user->role_type) }}
                                </span>
                            </td>
                            <td class="table-cell">{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
                            <td class="table-cell">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-skuul-600 hover:text-skuul-500">View</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="ml-2 text-gray-500 hover:text-gray-700">Edit</a>
                                @if ($user->is_active && in_array($user->role_type, ['student', 'teacher', 'parent'], true))
                                    <form method="POST" action="{{ route('admin.impersonate', $user) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="ml-2 text-skuul-600 hover:text-skuul-500">Login as</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-cell text-center text-gray-500 py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
</x-app-layout>