<x-app-layout>
    <x-slot name="title">My Attendance</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">My Attendance</h1>
        <p class="text-gray-600 dark:text-slate-100">{{ $student?->user?->name ?? 'Student' }} attendance history</p>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Date</th>
                        <th class="table-header">Classroom</th>
                        <th class="table-header">Subject</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($attendance as $record)
                        <tr>
                            <td class="table-cell">{{ $record->date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $record->classroom?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $record->subject?->name ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="status-badge status-{{ $record->status }}">{{ ucfirst($record->status) }}</span>
                            </td>
                            <td class="table-cell">{{ $record->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($attendance, 'links'))
            {{ $attendance->links() }}
        @endif
    </div>
</x-app-layout>
