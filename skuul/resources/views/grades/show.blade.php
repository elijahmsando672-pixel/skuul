<x-app-layout>
    <x-slot name="title">Grade Details</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Grade Details</h1>
        <p class="text-gray-600 dark:text-slate-100">Assessment record information</p>
    </div>

    <div class="card max-w-2xl">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Student</dt>
                <dd class="mt-1">{{ $grade->student?->user?->name ?? 'N/A' }} ({{ $grade->student?->student_id ?? 'N/A' }})</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Subject</dt>
                <dd class="mt-1">{{ $grade->subject?->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Assessment Type</dt>
                <dd class="mt-1">{{ ucfirst($grade->assessment_type) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Score</dt>
                <dd class="mt-1">{{ $grade->score }} / {{ $grade->max_score }} ({{ $grade->percentage() }}%)</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Weight</dt>
                <dd class="mt-1">{{ $grade->weight }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Date Recorded</dt>
                <dd class="mt-1">{{ $grade->date_recorded?->format('M d, Y') ?? 'N/A' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Remarks</dt>
                <dd class="mt-1">{{ $grade->remarks ?? '-' }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('teacher.grades.edit', $grade) }}" class="btn-primary">Edit Grade</a>
            <a href="{{ route('teacher.grades.index') }}" class="btn-secondary">Back to Grades</a>
        </div>
    </div>
</x-app-layout>
