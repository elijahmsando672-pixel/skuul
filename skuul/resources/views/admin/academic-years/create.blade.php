<x-app-layout>
    <x-slot name="title">Create Academic Year</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Create Academic Year</h2>
            <form method="POST" action="{{ route('admin.academic-years.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="form-label">Name</label>
                    <input id="name" type="text" name="name" class="input-field" value="{{ old('name') }}" required placeholder="e.g., 2026-2027">
                </div>
                <div>
                    <label for="start_date" class="form-label">Start Date</label>
                    <input id="start_date" type="date" name="start_date" class="input-field" value="{{ old('start_date') }}" required>
                </div>
                <div>
                    <label for="end_date" class="form-label">End Date</label>
                    <input id="end_date" type="date" name="end_date" class="input-field" value="{{ old('end_date') }}" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', false))>
                    <label for="is_active" class="form-label mb-0">Active</label>
                </div>
                <button type="submit" class="btn-primary w-full">Create Academic Year</button>
            </form>
        </div>
        <a href="{{ route('admin.academic-years.index') }}" class="btn-secondary w-full">Back to Academic Years</a>
    </div>
</x-app-layout>
