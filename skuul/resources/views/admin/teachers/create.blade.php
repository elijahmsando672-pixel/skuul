<x-app-layout>
    <x-slot name="title">Create Teacher</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Create Teacher</h2>
            <form method="POST" action="{{ route('admin.teachers.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="user_id" class="form-label">User</label>
                    <select id="user_id" name="user_id" class="input-field" required>
                        @forelse($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                        @empty
                            <option value="" disabled selected>No available teacher users</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label for="employee_id" class="form-label">Employee ID</label>
                    <input id="employee_id" type="text" name="employee_id" class="input-field" value="{{ old('employee_id') }}" required>
                </div>
                <div>
                    <label for="department" class="form-label">Department</label>
                    <input id="department" type="text" name="department" class="input-field" value="{{ old('department') }}">
                </div>
                <div>
                    <label for="hire_date" class="form-label">Hire Date</label>
                    <input id="hire_date" type="date" name="hire_date" class="input-field" value="{{ old('hire_date') }}">
                </div>
                <div>
                    <label for="qualification" class="form-label">Qualification</label>
                    <input id="qualification" type="text" name="qualification" class="input-field" value="{{ old('qualification') }}">
                </div>
                <div>
                    <label for="specialization" class="form-label">Specialization</label>
                    <input id="specialization" type="text" name="specialization" class="input-field" value="{{ old('specialization') }}">
                </div>
                <button type="submit" class="btn-primary w-full">Create Teacher</button>
            </form>
        </div>
        <a href="{{ route('admin.teachers.index') }}" class="btn-secondary w-full">Back to Teachers</a>
    </div>
</x-app-layout>
