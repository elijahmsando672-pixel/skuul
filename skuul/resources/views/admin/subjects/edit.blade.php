<x-app-layout>
    <x-slot name="title">Edit Subject</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Edit Subject</h2>
            <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="form-label">Name</label>
                        <input id="name" type="text" name="name" class="input-field" value="{{ old('name', $subject->name) }}" required>
                    </div>
                    <div>
                        <label for="code" class="form-label">Code</label>
                        <input id="code" type="text" name="code" class="input-field" value="{{ old('code', $subject->code) }}" required>
                    </div>
                    <div>
                        <label for="classroom_id" class="form-label">Classroom</label>
                        <select id="classroom_id" name="classroom_id" class="input-field" required>
                            @forelse($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" @selected(old('classroom_id', $subject->classroom_id) == $classroom->id)>{{ $classroom->name }} ({{ $classroom->academicYear?->name ?? 'N/A' }})</option>
                            @empty
                                <option value="" disabled>No classrooms available</option>
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label for="teacher_id" class="form-label">Teacher</label>
                        <select id="teacher_id" name="teacher_id" class="input-field" required>
                            @forelse($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('teacher_id', $subject->teacher_id) == $teacher->id)>{{ $teacher->user?->name ?? 'N/A' }}</option>
                            @empty
                                <option value="" disabled>No teachers available</option>
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label for="credits" class="form-label">Credits</label>
                        <input id="credits" type="number" name="credits" class="input-field" value="{{ old('credits', $subject->credits) }}" min="1">
                    </div>
                </div>
                <div>
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="input-field" rows="3">{{ old('description', $subject->description) }}</textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_compulsory" value="0">
                    <input id="is_compulsory" type="checkbox" name="is_compulsory" value="1" class="rounded border-gray-300" @checked(old('is_compulsory', $subject->is_compulsory))>
                    <label for="is_compulsory" class="form-label mb-0">Compulsory</label>
                </div>
                <button type="submit" class="btn-primary w-full">Update Subject</button>
            </form>
        </div>
        <a href="{{ route('admin.subjects.index') }}" class="btn-secondary w-full">Back to Subjects</a>
    </div>
</x-app-layout>
