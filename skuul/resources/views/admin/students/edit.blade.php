<x-app-layout>
    <x-slot name="title">Edit Student</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Edit Student</h2>
            <form method="POST" action="{{ route('admin.students.update', $student) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="user_id" class="form-label">User</label>
                    <select id="user_id" name="user_id" class="input-field" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id', $student->user_id) == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="student_id" class="form-label">Student ID</label>
                    <input id="student_id" type="text" name="student_id" class="input-field" value="{{ old('student_id', $student->student_id) }}" required>
                </div>
                <div>
                    <label for="grade_level" class="form-label">Grade Level</label>
                    <input id="grade_level" type="text" name="grade_level" class="input-field" value="{{ old('grade_level', $student->grade_level) }}" required>
                </div>
                <div>
                    <label for="enrollment_date" class="form-label">Enrollment Date</label>
                    <input id="enrollment_date" type="date" name="enrollment_date" class="input-field" value="{{ old('enrollment_date', $student->enrollment_date?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label for="parent_id" class="form-label">Parent</label>
                    <select id="parent_id" name="parent_id" class="input-field">
                        <option value="">No parent assigned</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" @selected(old('parent_id', $student->parent_id) == $parent->id)>{{ $parent->user?->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="emergency_contact" class="form-label">Emergency Contact</label>
                    <input id="emergency_contact" type="text" name="emergency_contact" class="input-field" value="{{ old('emergency_contact', $student->emergency_contact) }}">
                </div>
                <div>
                    <label for="medical_info" class="form-label">Medical Info</label>
                    <textarea id="medical_info" name="medical_info" class="input-field" rows="3">{{ old('medical_info', $student->medical_info) }}</textarea>
                </div>
                <button type="submit" class="btn-primary w-full">Update Student</button>
            </form>
        </div>
        <a href="{{ route('admin.students.index') }}" class="btn-secondary w-full">Back to Students</a>
    </div>
</x-app-layout>
