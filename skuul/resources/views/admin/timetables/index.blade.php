<x-app-layout>
    <x-slot name="title">Timetable</x-slot>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Timetable</h1>
            <p class="text-gray-600 dark:text-slate-400">Manage weekly class schedules</p>
        </div>
        <a href="{{ route('admin.timetables.create') }}" class="btn-primary">Add Entry</a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Day</th>
                        <th class="table-header">Time</th>
                        <th class="table-header">Classroom</th>
                        <th class="table-header">Subject</th>
                        <th class="table-header">Teacher</th>
                        <th class="table-header">Room</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($timetables as $timetable)
                        <tr>
                            <td class="table-cell font-medium">{{ $timetable->day_of_week }}</td>
                            <td class="table-cell">{{ $timetable->start_time }} - {{ $timetable->end_time }}</td>
                            <td class="table-cell">{{ $timetable->classroom->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $timetable->subject->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $timetable->teacher?->user->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $timetable->room ?? '-' }}</td>
                            <td class="table-cell">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.timetables.edit', $timetable) }}" class="text-skuul-600 hover:text-skuul-500">Edit</a>
                                    <form method="POST" action="{{ route('admin.timetables.destroy', $timetable) }}" onsubmit="return confirm('Delete this timetable entry?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-500">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">No timetable entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
