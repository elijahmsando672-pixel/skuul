<x-app-layout>
    <x-slot name="title">Student Dashboard</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Student Dashboard</h1>
        <p class="text-gray-600 dark:text-slate-100">Welcome, {{ $student?->user?->name ?? 'Student' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card">
            <p class="text-sm text-gray-500 dark:text-slate-100 uppercase tracking-wider">Grade Level</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $student->grade_level ?? 'N/A' }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500 dark:text-slate-100 uppercase tracking-wider">Attendance Records</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $attendance->count() }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500 dark:text-slate-100 uppercase tracking-wider">Grade Records</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $grades->count() }}</p>
        </div>
    </div>
</x-app-layout>