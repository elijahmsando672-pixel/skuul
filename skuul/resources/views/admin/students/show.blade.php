<x-app-layout>
    <x-slot name="title">Student Details</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $student->user?->name ?? 'Student' }}</h1>
        <p class="text-gray-600 dark:text-slate-100">Student profile and enrollment details</p>
    </div>

    <div class="card max-w-3xl">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Student ID</dt>
                <dd class="mt-1">{{ $student->student_id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Email</dt>
                <dd class="mt-1">{{ $student->user?->email ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Grade Level</dt>
                <dd class="mt-1">{{ $student->grade_level }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Parent</dt>
                <dd class="mt-1">{{ $student->parent?->user?->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Enrollment Date</dt>
                <dd class="mt-1">{{ $student->enrollment_date?->format('M d, Y') ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Emergency Contact</dt>
                <dd class="mt-1">{{ $student->emergency_contact ?? '-' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Medical Info</dt>
                <dd class="mt-1 whitespace-pre-line">{{ $student->medical_info ?? '-' }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.students.edit', $student) }}" class="btn-primary">Edit Student</a>
            <a href="{{ route('admin.students.index') }}" class="btn-secondary">Back to Students</a>
        </div>
    </div>

    <div class="card mt-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Enrollments</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Classroom</th>
                        <th class="table-header">Academic Year</th>
                        <th class="table-header">Enrollment Date</th>
                        <th class="table-header">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($student->enrollments as $enrollment)
                        <tr>
                            <td class="table-cell">{{ $enrollment->classroom?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $enrollment->academicYear?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $enrollment->enrollment_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="table-cell">{{ ucfirst($enrollment->status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">No enrollments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
