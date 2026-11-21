<x-app-layout>
    <x-slot name="title">Transport Assignments</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Children Transport</h1>
        <p class="text-gray-600">Active transport assignments for your children</p>
    </div>

    <div class="card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Active Assignments</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Student</th>
                        <th class="table-header">Route</th>
                        <th class="table-header">Vehicle</th>
                        <th class="table-header">Pickup Point</th>
                        <th class="table-header">Academic Year</th>
                        <th class="table-header">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($assignments as $assignment)
                        <tr>
                            <td class="table-cell font-medium">{{ $assignment->assignable?->user?->name ?? 'N/A' }}</td>
                            <td class="table-cell">
                                {{ $assignment->transportRoute->name ?? 'N/A' }}
                                <span class="block text-xs text-gray-500">
                                    {{ $assignment->transportRoute->origin ?? 'N/A' }} to {{ $assignment->transportRoute->destination ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="table-cell">
                                {{ $assignment->vehicle->registration_number ?? 'N/A' }}
                                <span class="block text-xs text-gray-500">{{ $assignment->vehicle->driver_name ?? 'Driver not assigned' }}</span>
                            </td>
                            <td class="table-cell">{{ $assignment->pickup_point ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $assignment->academicYear->name ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="status-badge">{{ $assignment->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-cell text-center text-gray-500 py-4">No active transport assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
