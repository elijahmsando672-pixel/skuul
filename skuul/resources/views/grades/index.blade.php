<x-app-layout>
    <x-slot name="title">Grades</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Grades</h1>
            <p class="text-gray-600 dark:text-slate-100">Manage student grades and assessments</p>
        </div>
        <a href="{{ route('teacher.grades.create') }}" class="btn-primary">Record Grade</a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Student</th>
                        <th class="table-header">Subject</th>
                        <th class="table-header">Assessment</th>
                        <th class="table-header">Score</th>
                        <th class="table-header">Date</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($grades as $grade)
                        <tr>
                            <td class="table-cell font-medium">{{ $grade->student?->user?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $grade->subject?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ ucfirst($grade->assessment_type) }}</td>
                            <td class="table-cell">{{ $grade->score }} / {{ $grade->max_score }} ({{ $grade->percentage() }}%)</td>
                            <td class="table-cell">{{ $grade->date_recorded?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <a href="{{ route('teacher.grades.show', $grade) }}" class="text-skuul-600 hover:text-skuul-500">View</a>
                                <a href="{{ route('teacher.grades.edit', $grade) }}" class="ml-2 text-gray-500 dark:text-slate-100 hover:text-gray-700">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">No grades found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($grades, 'links'))
            {{ $grades->links() }}
        @endif
    </div>
</x-app-layout>
