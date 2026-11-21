<x-app-layout>
    <x-slot name="title">My Exam Results</x-slot>

    @php
        $gradeClasses = [
            'A' => 'bg-green-100 text-green-800',
            'B' => 'bg-blue-100 text-blue-800',
            'C' => 'bg-yellow-100 text-yellow-800',
            'D' => 'bg-orange-100 text-orange-800',
            'F' => 'bg-red-100 text-red-800',
        ];
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Exam Results</h1>
        <p class="text-gray-600">Published examination results</p>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Exam</th>
                        <th class="table-header">Subject</th>
                        <th class="table-header">Date</th>
                        <th class="table-header">Marks</th>
                        <th class="table-header">Percentage</th>
                        <th class="table-header">Grade</th>
                        <th class="table-header">Remarks</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($results as $result)
                        <tr>
                            <td class="table-cell font-medium">{{ $result->exam->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $result->exam->subject->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $result->exam->exam_date?->format('M d, Y') }}</td>
                            <td class="table-cell">{{ $result->marks_obtained }} / {{ $result->exam->total_marks }}</td>
                            <td class="table-cell">{{ $result->percentage }}%</td>
                            <td class="table-cell">
                                @if($result->grade)
                                    <span class="status-badge {{ $gradeClasses[$result->grade] ?? 'bg-gray-100 text-gray-800' }}">{{ $result->grade }}</span>
                                @else
                                    <span class="status-badge bg-gray-100 text-gray-800">Not graded</span>
                                @endif
                            </td>
                            <td class="table-cell">{{ $result->remarks ?? '--' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-cell text-center text-gray-500 py-4">No published exam results found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
