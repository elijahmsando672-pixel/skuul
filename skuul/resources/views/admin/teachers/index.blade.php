<x-app-layout>
    <x-slot name="title">Teachers</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Teachers</h1>
            <p class="text-gray-600">Manage teaching staff</p>
        </div>
        <a href="{{ route('admin.teachers.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2"><path d="M12 4v16m8-8H4"/></svg>
            Add Teacher
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Employee ID</th>
                        <th class="table-header">Name</th>
                        <th class="table-header">Department</th>
                        <th class="table-header">Hire Date</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($teachers as $teacher)
                        <tr>
                            <td class="table-cell font-medium">{{ $teacher->employee_id }}</td>
                            <td class="table-cell">{{ $teacher->user->name }}</td>
                            <td class="table-cell">{{ $teacher->department ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $teacher->hire_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <a href="{{ route('admin.teachers.show', $teacher) }}" class="text-skuul-600 hover:text-skuul-500">View</a>
                                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="ml-2 text-gray-500 hover:text-gray-700">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-cell text-center text-gray-500 py-4">No teachers found.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $teachers->links() }}
    </div>
</x-app-layout>