<x-app-layout>
    <x-slot name="title">Edit Classroom</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Edit Classroom</h2>
            <form method="POST" action="{{ route('admin.classrooms.update', $classroom) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="form-label">Name</label>
                    <input id="name" type="text" name="name" class="input-field" value="{{ old('name', $classroom->name) }}" required>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="grade_level" class="form-label">Grade Level</label>
                        <input id="grade_level" type="text" name="grade_level" class="input-field" value="{{ old('grade_level', $classroom->grade_level) }}" required>
                    </div>
                    <div>
                        <label for="section" class="form-label">Section</label>
                        <input id="section" type="text" name="section" class="input-field" value="{{ old('section', $classroom->section) }}" maxlength="10">
                    </div>
                    <div>
                        <label for="academic_year_id" class="form-label">Academic Year</label>
                        <select id="academic_year_id" name="academic_year_id" class="input-field" required>
                            @forelse($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}" @selected(old('academic_year_id', $classroom->academic_year_id) == $academicYear->id)>{{ $academicYear->name }}</option>
                            @empty
                                <option value="" disabled>No academic years available</option>
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label for="teacher_id" class="form-label">Homeroom Teacher</label>
                        <select id="teacher_id" name="teacher_id" class="input-field">
                            <option value="">Unassigned</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('teacher_id', $classroom->teacher_id) == $teacher->id)>{{ $teacher->user?->name ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="room_number" class="form-label">Room Number</label>
                        <input id="room_number" type="text" name="room_number" class="input-field" value="{{ old('room_number', $classroom->room_number) }}" maxlength="20">
                    </div>
                    <div>
                        <label for="capacity" class="form-label">Capacity</label>
                        <input id="capacity" type="number" name="capacity" class="input-field" value="{{ old('capacity', $classroom->capacity) }}" min="1">
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full">Update Classroom</button>
            </form>
        </div>
        <a href="{{ route('admin.classrooms.index') }}" class="btn-secondary w-full">Back to Classrooms</a>
    </div>
</x-app-layout>
