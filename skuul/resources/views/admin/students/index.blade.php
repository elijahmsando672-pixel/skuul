<x-app-layout>
    <x-slot name="title">Students</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Students</h1>
            <p class="text-gray-600">Manage student enrollment and records</p>
        </div>
        <a href="{{ route('admin.students.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2"><path d="M12 4v16m8-8H4"/></svg>
            Add Student
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Student ID</th>
                        <th class="table-header">Name</th>
                        <th class="table-header">Grade Level</th>
                        <th class="table-header">Parent</th>
                        <th class="table-header">Enrollment Date</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($students as $student)
                        <tr>
                            <td class="table-cell font-medium">{{ $student->student_id }}</td>
                            <td class="table-cell">{{ $student->user->name }}</td>
                            <td class="table-cell">{{ $student->grade_level }}</td>
                            <td class="table-cell">{{ $student->parent?->user->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $student->enrollment_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <a href="{{ route('admin.students.show', $student) }}" class="text-skuul-600 hover:text-skuul-500">View</a>
                                <a href="{{ route('admin.students.edit', $student) }}" class="ml-2 text-gray-500 hover:text-gray-700">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-cell text-center text-gray-500 py-4">No students found.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $students->links() }}
    </div>
</x-app-layout>