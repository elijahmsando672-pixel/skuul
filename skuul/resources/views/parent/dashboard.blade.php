<x-app-layout>
    <x-slot name="title">Parent Dashboard</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Parent Dashboard</h1>
        <p class="text-gray-600">Welcome, {{ $parent?->user->name ?? 'Parent' }}</p>
    </div>

    <div class="card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Children</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Student ID</th>
                        <th class="table-header">Name</th>
                        <th class="table-header">Grade Level</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($children as $child)
                        <tr>
                            <td class="table-cell font-medium">{{ $child->student_id }}</td>
                            <td class="table-cell">{{ $child->user->name }}</td>
                            <td class="table-cell">{{ $child->grade_level }}</td>
                            <td class="table-cell">
                                <a href="{{ route('parent.attendance.index', $child) }}" class="text-skuul-600 hover:text-skuul-500">Attendance</a>
                                <span class="text-gray-400"> | </span>
                                <a href="{{ route('parent.grades.index', $child) }}" class="text-skuul-600 hover:text-skuul-500">Grades</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="table-cell text-center text-gray-500 py-4">No children found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>