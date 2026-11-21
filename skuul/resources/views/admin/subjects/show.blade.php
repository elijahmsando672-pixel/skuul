<x-app-layout>
    <x-slot name="title">Subject Details</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $subject->name }}</h1>
        <p class="text-gray-600 dark:text-slate-100">Subject details and assignment</p>
    </div>

    <div class="card max-w-2xl">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Code</dt>
                <dd class="mt-1">{{ $subject->code }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Credits</dt>
                <dd class="mt-1">{{ $subject->credits ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Classroom</dt>
                <dd class="mt-1">{{ $subject->classroom?->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Teacher</dt>
                <dd class="mt-1">{{ $subject->teacher?->user?->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Type</dt>
                <dd class="mt-1">{{ $subject->is_compulsory ? 'Compulsory' : 'Elective' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Description</dt>
                <dd class="mt-1 whitespace-pre-line">{{ $subject->description ?? '-' }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn-primary">Edit Subject</a>
            <a href="{{ route('admin.subjects.index') }}" class="btn-secondary">Back to Subjects</a>
        </div>
    </div>
</x-app-layout>
