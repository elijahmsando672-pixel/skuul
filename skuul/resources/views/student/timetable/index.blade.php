<x-app-layout>
    <x-slot name="title">My Timetable</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">My Timetable</h1>
        <p class="text-gray-600 dark:text-slate-400">Your weekly class schedule</p>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Day</th>
                        <th class="table-header">Time</th>
                        <th class="table-header">Subject</th>
                        <th class="table-header">Teacher</th>
                        <th class="table-header">Room</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($timetables as $timetable)
                        <tr>
                            <td class="table-cell font-medium">{{ $timetable->day_of_week }}</td>
                            <td class="table-cell">{{ $timetable->start_time }} - {{ $timetable->end_time }}</td>
                            <td class="table-cell">{{ $timetable->subject->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $timetable->teacher?->user->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $timetable->room ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No timetable entries found for your class.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
