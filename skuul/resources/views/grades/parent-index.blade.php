<x-app-layout>
    <x-slot name="title">Child Grades</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Grades for {{ $student?->user?->name ?? 'Student' }}</h1>
        <p class="text-gray-600 dark:text-slate-100">Assessment results for your child</p>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Subject</th>
                        <th class="table-header">Assessment</th>
                        <th class="table-header">Score</th>
                        <th class="table-header">Percentage</th>
                        <th class="table-header">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($grades as $grade)
                        <tr>
                            <td class="table-cell font-medium">{{ $grade->subject?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ ucfirst($grade->assessment_type) }}</td>
                            <td class="table-cell">{{ $grade->score }} / {{ $grade->max_score }}</td>
                            <td class="table-cell">{{ $grade->percentage() }}%</td>
                            <td class="table-cell">{{ $grade->date_recorded?->format('M d, Y') ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No grades found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($grades, 'links'))
            {{ $grades->links() }}
        @endif
    </div>

    <a href="{{ route('parent.dashboard') }}" class="btn-secondary mt-6">Back to Dashboard</a>
</x-app-layout>
