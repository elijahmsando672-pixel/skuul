<x-app-layout>
    <x-slot name="title">Teacher Dashboard</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Teacher Dashboard</h1>
        <p class="text-gray-600 dark:text-slate-100">Welcome, {{ $teacher?->user?->name ?? 'Teacher' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card">
            <p class="text-sm text-gray-500 dark:text-slate-100 uppercase tracking-wider">Subjects</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $subjects->count() }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500 dark:text-slate-100 uppercase tracking-wider">Recent Attendance</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $recentAttendance->count() }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500 dark:text-slate-100 uppercase tracking-wider">Recent Grades</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $recentGrades->count() }}</p>
        </div>
    </div>
</x-app-layout>