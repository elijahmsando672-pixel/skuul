<x-app-layout>
    <x-slot name="title">My Transport</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">My Transport</h1>
        <p class="text-gray-600 dark:text-slate-100">Welcome, {{ $student?->user?->name ?? 'Student' }}</p>
    </div>

    <div class="card">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Active Transport Assignment</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Route</th>
                        <th class="table-header">Vehicle</th>
                        <th class="table-header">Pickup Point</th>
                        <th class="table-header">Academic Year</th>
                        <th class="table-header">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($assignments as $assignment)
                        <tr>
                            <td class="table-cell font-medium">
                                {{ $assignment->transportRoute->name ?? 'N/A' }}
                                <span class="block text-xs text-gray-500 dark:text-slate-100">
                                    {{ $assignment->transportRoute->origin ?? 'N/A' }} to {{ $assignment->transportRoute->destination ?? 'N/A' }}
                                </span>
                                @if($assignment->transportRoute?->estimated_minutes)
                                    <span class="block text-xs text-gray-500 dark:text-slate-100">{{ $assignment->transportRoute->estimated_minutes }} minutes estimated</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                {{ $assignment->vehicle->registration_number ?? 'N/A' }}
                                <span class="block text-xs text-gray-500 dark:text-slate-100">{{ $assignment->vehicle->driver_name ?? 'Driver not assigned' }}</span>
                            </td>
                            <td class="table-cell">{{ $assignment->pickup_point ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $assignment->academicYear->name ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="status-badge">{{ $assignment->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-cell text-center text-gray-500 dark:text-slate-100 py-4">No active transport assignment found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
