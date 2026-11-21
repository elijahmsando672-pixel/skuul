<x-app-layout>
    <x-slot name="title">Edit Exam</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Edit Exam</h2>
            <form method="POST" action="{{ route('admin.exams.update', $exam) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="academic_year_id" class="form-label">Academic Year</label>
                    <select id="academic_year_id" name="academic_year_id" class="select-field" required>
                        @forelse($academicYears as $year)
                            <option value="{{ $year->id }}" @selected(old('academic_year_id', $exam->academic_year_id) == $year->id)>{{ $year->name }}</option>
                        @empty
                            <option value="" disabled>No academic years available</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label for="subject_id" class="form-label">Subject</label>
                    <select id="subject_id" name="subject_id" class="select-field" required>
                        @forelse($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id', $exam->subject_id) == $subject->id)>{{ $subject->name }} ({{ $subject->classroom->name ?? 'N/A' }})</option>
                        @empty
                            <option value="" disabled>No subjects available</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label for="name" class="form-label">Exam Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $exam->name) }}" class="input-field" required>
                </div>
                <div>
                    <label for="exam_type" class="form-label">Exam Type</label>
                    <input type="text" id="exam_type" name="exam_type" value="{{ old('exam_type', $exam->exam_type) }}" class="input-field" required>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="exam_date" class="form-label">Exam Date</label>
                        <input type="date" id="exam_date" name="exam_date" value="{{ old('exam_date', $exam->exam_date?->toDateString()) }}" class="input-field" required>
                    </div>
                    <div>
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" id="start_time" name="start_time" value="{{ old('start_time', $exam->start_time) }}" class="input-field">
                    </div>
                    <div>
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" id="end_time" name="end_time" value="{{ old('end_time', $exam->end_time) }}" class="input-field">
                    </div>
                </div>
                <div>
                    <label for="total_marks" class="form-label">Total Marks</label>
                    <input type="number" id="total_marks" name="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" step="0.01" min="0.01" class="input-field" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_published" name="is_published" value="1" class="rounded border-gray-300" @checked(old('is_published', $exam->is_published))>
                    <label for="is_published" class="form-label mb-0">Published</label>
                </div>
                <button type="submit" class="btn-primary w-full">Update Exam</button>
            </form>
        </div>
        <a href="{{ route('admin.exams.index') }}" class="btn-secondary w-full">Back to Exams</a>
    </div>
</x-app-layout>
