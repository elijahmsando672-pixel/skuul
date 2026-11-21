<x-app-layout>
    <x-slot name="title">Edit Grade</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Edit Grade</h2>
            <form method="POST" action="{{ route('teacher.grades.update', $grade) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="student_id" class="form-label">Student</label>
                    <select id="student_id" name="student_id" class="input-field" required>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" @selected(old('student_id', $grade->student_id) == $student->id)>{{ $student->student_id }} - {{ $student->user?->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="subject_id" class="form-label">Subject</label>
                    <select id="subject_id" name="subject_id" class="input-field" required>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id', $grade->subject_id) == $subject->id)>{{ $subject->code }} - {{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="assessment_type" class="form-label">Assessment Type</label>
                        <select id="assessment_type" name="assessment_type" class="input-field" required>
                            @foreach(['quiz' => 'Quiz', 'assignment' => 'Assignment', 'midterm' => 'Midterm', 'final' => 'Final', 'project' => 'Project'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('assessment_type', $grade->assessment_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="date_recorded" class="form-label">Date Recorded</label>
                        <input id="date_recorded" type="date" name="date_recorded" class="input-field" value="{{ old('date_recorded', $grade->date_recorded?->format('Y-m-d')) }}" required>
                    </div>
                    <div>
                        <label for="score" class="form-label">Score</label>
                        <input id="score" type="number" name="score" class="input-field" value="{{ old('score', $grade->score) }}" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label for="max_score" class="form-label">Max Score</label>
                        <input id="max_score" type="number" name="max_score" class="input-field" value="{{ old('max_score', $grade->max_score) }}" step="0.01" min="1" required>
                    </div>
                    <div>
                        <label for="weight" class="form-label">Weight</label>
                        <input id="weight" type="number" name="weight" class="input-field" value="{{ old('weight', $grade->weight) }}" step="0.01" min="0">
                    </div>
                </div>
                <div>
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea id="remarks" name="remarks" class="input-field" rows="3">{{ old('remarks', $grade->remarks) }}</textarea>
                </div>
                <button type="submit" class="btn-primary w-full">Update Grade</button>
            </form>
        </div>
        <a href="{{ route('teacher.grades.index') }}" class="btn-secondary w-full">Back to Grades</a>
    </div>
</x-app-layout>
