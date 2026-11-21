<x-app-layout>
    <x-slot name="title">Exams</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Exams</h1>
            <p class="text-gray-600">Manage examinations and results</p>
        </div>
        <a href="{{ route('admin.exams.create') }}" class="btn-primary">Create Exam</a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Exam</th>
                        <th class="table-header">Subject</th>
                        <th class="table-header">Academic Year</th>
                        <th class="table-header">Type</th>
                        <th class="table-header">Date</th>
                        <th class="table-header">Total Marks</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($exams as $exam)
                        <tr>
                            <td class="table-cell font-medium">{{ $exam->name }}</td>
                            <td class="table-cell">{{ $exam->subject->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $exam->academicYear->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ ucfirst($exam->exam_type) }}</td>
                            <td class="table-cell">{{ $exam->exam_date?->format('M d, Y') }}</td>
                            <td class="table-cell">{{ $exam->total_marks }}</td>
                            <td class="table-cell">
                                <span class="status-badge {{ $exam->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $exam->is_published ? 'Published' : 'Draft' }}</span>
                            </td>
                            <td class="table-cell">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.exams.results', $exam) }}" class="btn-secondary">Results</a>
                                    <form method="POST" action="{{ route('admin.exams.publish', $exam) }}">
                                        @csrf
                                        <button type="submit" class="btn-secondary">{{ $exam->is_published ? 'Unpublish' : 'Publish' }}</button>
                                    </form>
                                    <a href="{{ route('admin.exams.edit', $exam) }}" class="btn-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" onsubmit="return confirm('Delete this exam?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="table-cell text-center text-gray-500 py-4">No exams found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $exams->links() }}
    </div>
</x-app-layout>
