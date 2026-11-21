<x-app-layout>
    <x-slot name="title">Create Fee Structure</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Create New Fee Structure</h2>
            <form method="POST" action="{{ route('admin.fees.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="academic_year_id" class="form-label">Academic Year</label>
                    <select id="academic_year_id" name="academic_year_id" class="input-field" required>
                        @forelse($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" @selected(old('academic_year_id') == $academicYear->id)>{{ $academicYear->name }}</option>
                        @empty
                            <option value="" disabled selected>No academic years available</option>
                        @endforelse
                    </select>
                    @error('academic_year_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="name" class="form-label">Name</label>
                    <input id="name" type="text" name="name" class="input-field" value="{{ old('name') }}" required placeholder="e.g., Tuition Fee">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="amount" class="form-label">Amount</label>
                    <input id="amount" type="number" name="amount" class="input-field" value="{{ old('amount') }}" step="0.01" min="0" required>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="frequency" class="form-label">Frequency</label>
                    <select id="frequency" name="frequency" class="input-field" required>
                        <option value="monthly" @selected(old('frequency', 'monthly') === 'monthly')>Monthly</option>
                        <option value="termly" @selected(old('frequency') === 'termly')>Termly</option>
                        <option value="annual" @selected(old('frequency') === 'annual')>Annual</option>
                    </select>
                    @error('frequency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="due_date" class="form-label">Due Date</label>
                    <input id="due_date" type="date" name="due_date" class="input-field" value="{{ old('due_date') }}">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', true))>
                    <label for="is_active" class="form-label mb-0">Active</label>
                </div>
                <button type="submit" class="btn-primary w-full">Create Fee Structure</button>
            </form>
        </div>
        <a href="{{ route('admin.fees.index') }}" class="btn-secondary w-full">Back to Fee Structures</a>
    </div>
</x-app-layout>
