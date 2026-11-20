<x-app-layout>
    <x-slot name="title">Record Grade</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Record Student Grade</h2>
            <form method="POST" action="{{ route('grades.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                    <select name="student_id" class="input-field" required>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->student_id }} - {{ $student->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <select name="subject_id" class="input-field" required>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assessment Type</label>
                        <select name="assessment_type" class="input-field" required>
                            <option value="quiz">Quiz</option>
                            <option value="assignment">Assignment</option>
                            <option value="midterm">Midterm</option>
                            <option value="final">Final</option>
                            <option value="project">Project</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Score</label>
                        <input type="number" name="score" step="0.01" min="0" class="input-field" required placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Score</label>
                        <input type="number" name="max_score" step="0.01" min="0.01" value="100" class="input-field" required placeholder="100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Weight</label>
                        <input type="number" name="weight" step="0.01" min="0" value="1.00" class="input-field" placeholder="1.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Recorded</label>
                        <input type="date" name="date_recorded" class="input-field" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea name="remarks" class="input-field" rows="3"></textarea>
                </div>
                <button type="submit" class="btn-primary w-full">Record Grade</button>
            </form>
        </div>
        <a href="{{ route('grades.index') }}" class="btn-secondary w-full">Back to Grades</a>
    </div>
</x-app-layout>