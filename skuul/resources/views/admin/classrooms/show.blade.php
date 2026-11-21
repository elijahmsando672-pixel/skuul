<x-app-layout>
    <x-slot name="title">Classroom Details</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $classroom->name }}</h1>
        <p class="text-gray-600 dark:text-slate-100">Classroom details and assignments</p>
    </div>

    <div class="card max-w-2xl">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Grade Level</dt>
                <dd class="mt-1">{{ $classroom->grade_level }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Section</dt>
                <dd class="mt-1">{{ $classroom->section ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Academic Year</dt>
                <dd class="mt-1">{{ $classroom->academicYear?->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Homeroom Teacher</dt>
                <dd class="mt-1">{{ $classroom->teacher?->user?->name ?? 'Unassigned' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Room Number</dt>
                <dd class="mt-1">{{ $classroom->room_number ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Capacity</dt>
                <dd class="mt-1">{{ $classroom->capacity ?? 'N/A' }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="btn-primary">Edit Classroom</a>
            <a href="{{ route('admin.classrooms.index') }}" class="btn-secondary">Back to Classrooms</a>
        </div>
    </div>
</x-app-layout>
