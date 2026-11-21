<x-app-layout>
    <x-slot name="title">Teacher Details</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $teacher->user?->name ?? 'Teacher' }}</h1>
        <p class="text-gray-600 dark:text-slate-100">Teacher profile and assignment details</p>
    </div>

    <div class="card max-w-2xl">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Employee ID</dt>
                <dd class="mt-1">{{ $teacher->employee_id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Email</dt>
                <dd class="mt-1">{{ $teacher->user?->email ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Department</dt>
                <dd class="mt-1">{{ $teacher->department ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Hire Date</dt>
                <dd class="mt-1">{{ $teacher->hire_date?->format('M d, Y') ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Qualification</dt>
                <dd class="mt-1">{{ $teacher->qualification ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Specialization</dt>
                <dd class="mt-1">{{ $teacher->specialization ?? '-' }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn-primary">Edit Teacher</a>
            <a href="{{ route('admin.teachers.index') }}" class="btn-secondary">Back to Teachers</a>
        </div>
    </div>
</x-app-layout>
