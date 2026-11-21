<x-app-layout>
    <x-slot name="title">Academic Year Details</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $year->name }}</h1>
        <p class="text-gray-600 dark:text-slate-100">Academic year details</p>
    </div>

    <div class="card max-w-2xl">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Start Date</dt>
                <dd class="mt-1">{{ $year->start_date?->format('M d, Y') ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">End Date</dt>
                <dd class="mt-1">{{ $year->end_date?->format('M d, Y') ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Status</dt>
                <dd class="mt-1">{{ $year->is_active ? 'Active' : 'Inactive' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Classrooms</dt>
                <dd class="mt-1">{{ $year->classrooms()->count() }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.academic-years.edit', $year) }}" class="btn-primary">Edit Academic Year</a>
            <a href="{{ route('admin.academic-years.index') }}" class="btn-secondary">Back to Academic Years</a>
        </div>
    </div>
</x-app-layout>
