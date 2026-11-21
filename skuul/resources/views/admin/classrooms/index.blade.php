<x-app-layout>
    <x-slot name="title">Classrooms</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Classrooms</h1>
            <p class="text-gray-600 dark:text-slate-100">Manage class sections</p>
        </div>
        <a href="{{ route('admin.classrooms.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2"><path d="M12 4v16m8-8H4"/></svg>
            Add Classroom
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Name</th>
                        <th class="table-header">Grade Level</th>
                        <th class="table-header">Section</th>
                        <th class="table-header">Academic Year</th>
                        <th class="table-header">Teacher</th>
                        <th class="table-header">Capacity</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($classrooms as $classroom)
                        <tr>
                            <td class="table-cell font-medium">{{ $classroom->name }}</td>
                            <td class="table-cell">{{ $classroom->grade_level }}</td>
                            <td class="table-cell">{{ $classroom->section }}</td>
                            <td class="table-cell">{{ $classroom->academicYear->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $classroom->teacher?->user?->name ?? 'Unassigned' }}</td>
                            <td class="table-cell">{{ $classroom->capacity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-cell text-center text-gray-500 dark:text-slate-100 py-4">No classrooms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $classrooms->links() }}
    </div>
</x-app-layout>