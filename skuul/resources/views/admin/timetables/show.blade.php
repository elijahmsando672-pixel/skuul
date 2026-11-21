<x-app-layout>
    <x-slot name="title">Timetable Entry</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Timetable Entry</h1>
        <p class="text-gray-600 dark:text-slate-400">View class schedule details</p>
    </div>

    <div class="card max-w-3xl">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Academic Year</dt>
                <dd class="mt-1">{{ $timetable->academicYear->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Classroom</dt>
                <dd class="mt-1">{{ $timetable->classroom->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Subject</dt>
                <dd class="mt-1">{{ $timetable->subject->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Teacher</dt>
                <dd class="mt-1">{{ $timetable->teacher?->user->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Day</dt>
                <dd class="mt-1">{{ $timetable->day_of_week }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Time</dt>
                <dd class="mt-1">{{ $timetable->start_time }} - {{ $timetable->end_time }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Room</dt>
                <dd class="mt-1">{{ $timetable->room ?? '-' }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('admin.timetables.edit', $timetable) }}" class="btn-primary">Edit</a>
            <a href="{{ route('admin.timetables.index') }}" class="btn-secondary">Back</a>
        </div>
    </div>
</x-app-layout>
