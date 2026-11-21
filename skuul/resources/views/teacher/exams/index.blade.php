<x-app-layout>
    <x-slot name="title">Exams</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Exams</h1>
        <p class="text-gray-600 dark:text-slate-100">Examinations for your subjects</p>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Exam</th>
                        <th class="table-header">Subject</th>
                        <th class="table-header">Classroom</th>
                        <th class="table-header">Academic Year</th>
                        <th class="table-header">Date</th>
                        <th class="table-header">Total Marks</th>
                        <th class="table-header">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($exams as $exam)
                        <tr>
                            <td class="table-cell font-medium">{{ $exam->name }}</td>
                            <td class="table-cell">{{ $exam->subject->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $exam->subject->classroom->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $exam->academicYear->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $exam->exam_date?->format('M d, Y') }}</td>
                            <td class="table-cell">{{ $exam->total_marks }}</td>
                            <td class="table-cell">
                                <span class="status-badge {{ $exam->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 dark:bg-slate-800/60 text-gray-800 dark:text-slate-100' }}">{{ $exam->is_published ? 'Published' : 'Draft' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-cell text-center text-gray-500 dark:text-slate-100 py-4">No exams found for your subjects.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
