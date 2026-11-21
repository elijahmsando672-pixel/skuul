<x-app-layout>
    <x-slot name="title">Edit Academic Year</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Edit Academic Year</h2>
            <form method="POST" action="{{ route('admin.academic-years.update', $year) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="form-label">Name</label>
                    <input id="name" type="text" name="name" class="input-field" value="{{ old('name', $year->name) }}" required>
                </div>
                <div>
                    <label for="start_date" class="form-label">Start Date</label>
                    <input id="start_date" type="date" name="start_date" class="input-field" value="{{ old('start_date', $year->start_date?->format('Y-m-d')) }}" required>
                </div>
                <div>
                    <label for="end_date" class="form-label">End Date</label>
                    <input id="end_date" type="date" name="end_date" class="input-field" value="{{ old('end_date', $year->end_date?->format('Y-m-d')) }}" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $year->is_active))>
                    <label for="is_active" class="form-label mb-0">Active</label>
                </div>
                <button type="submit" class="btn-primary w-full">Update Academic Year</button>
            </form>
        </div>
        <a href="{{ route('admin.academic-years.index') }}" class="btn-secondary w-full">Back to Academic Years</a>
    </div>
</x-app-layout>
