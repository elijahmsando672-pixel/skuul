<x-app-layout>
    <x-slot name="title">Exam Results</x-slot>

    @php
        $gradeClasses = [
            'A' => 'bg-green-100 text-green-800',
            'B' => 'bg-blue-100 text-blue-800',
            'C' => 'bg-yellow-100 text-yellow-800',
            'D' => 'bg-orange-100 text-orange-800',
            'F' => 'bg-red-100 text-red-800',
        ];
    @endphp

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $exam->name }}</h1>
            <p class="text-gray-600">{{ $exam->subject->name ?? 'N/A' }} &middot; {{ $exam->academicYear->name ?? 'N/A' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="status-badge {{ $exam->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $exam->is_published ? 'Published' : 'Draft' }}</span>
            <a href="{{ route('admin.exams.index') }}" class="btn-secondary">Back to Exams</a>
        </div>
    </div>

    <div class="card mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="block text-gray-500">Type</span>
                <span class="font-medium">{{ ucfirst($exam->exam_type) }}</span>
            </div>
            <div>
                <span class="block text-gray-500">Date</span>
                <span class="font-medium">{{ $exam->exam_date?->format('M d, Y') }}</span>
            </div>
            <div>
                <span class="block text-gray-500">Time</span>
                <span class="font-medium">{{ $exam->start_time ?? '--' }} - {{ $exam->end_time ?? '--' }}</span>
            </div>
            <div>
                <span class="block text-gray-500">Total Marks</span>
                <span class="font-medium">{{ $exam->total_marks }}</span>
            </div>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.exams.results.store', $exam) }}">
            @csrf
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-header">Student</th>
                            <th class="table-header">Marks Obtained</th>
                            <th class="table-header">Grade</th>
                            <th class="table-header">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($students as $student)
                            @php($result = $results->get($student->id))
                            <tr>
                                <td class="table-cell">
                                    <input type="hidden" name="results[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                    <span class="font-medium">{{ $student->user->name ?? 'N/A' }}</span>
                                    <span class="block text-xs text-gray-500">{{ $student->student_id }}</span>
                                </td>
                                <td class="table-cell">
                                    <input type="number" name="results[{{ $loop->index }}][marks_obtained]" value="{{ old('results.'.$loop->index.'.marks_obtained', $result?->marks_obtained) }}" step="0.01" min="0" max="{{ $exam->total_marks }}" class="input-field" placeholder="0">
                                </td>
                                <td class="table-cell">
                                    @if($result?->grade)
                                        <span class="status-badge {{ $gradeClasses[$result->grade] ?? 'bg-gray-100 text-gray-800' }}">{{ $result->grade }}</span>
                                    @else
                                        <span class="status-badge bg-gray-100 text-gray-800">Not graded</span>
                                    @endif
                                </td>
                                <td class="table-cell">
                                    <textarea name="results[{{ $loop->index }}][remarks]" rows="1" class="input-field" placeholder="Optional">{{ old('results.'.$loop->index.'.remarks', $result?->remarks) }}</textarea>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="table-cell text-center text-gray-500 py-4">No students available for this exam.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="btn-primary">Save Results</button>
            </div>
        </form>
    </div>
</x-app-layout>
