<x-app-layout>
    <x-slot name="title">Create Student</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Create New Student</h2>
            <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-100 mb-1">User</label>
                    <select name="user_id" class="input-field" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-100 mb-1">Student ID</label>
                    <input type="text" name="student_id" class="input-field" required placeholder="e.g., STU000123">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-100 mb-1">Grade Level</label>
                    <input type="text" name="grade_level" class="input-field" required placeholder="e.g., Grade 10">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-100 mb-1">Enrollment Date</label>
                    <input type="date" name="enrollment_date" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-100 mb-1">Parent</label>
                    <select name="parent_id" class="input-field">
                        <option value="">No parent assigned</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-100 mb-1">Emergency Contact</label>
                    <input type="text" name="emergency_contact" class="input-field" placeholder="Phone number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-100 mb-1">Medical Info</label>
                    <textarea name="medical_info" class="input-field" rows="3"></textarea>
                </div>
                <button type="submit" class="btn-primary w-full">Create Student</button>
            </form>
        </div>
        <a href="{{ route('admin.students.index') }}" class="btn-secondary w-full">Back to Students</a>
    </div>
</x-app-layout>