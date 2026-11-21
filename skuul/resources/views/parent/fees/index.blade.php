<x-app-layout>
    <x-slot name="title">My Fees</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Fees</h1>
        <p class="text-gray-600">Fee overview for {{ $children->first()?->user?->name ? 'your children' : 'your account' }}</p>
    </div>

    <div class="card mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Children</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Student ID</th>
                        <th class="table-header">Name</th>
                        <th class="table-header">Grade Level</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($children as $child)
                        <tr>
                            <td class="table-cell font-medium">{{ $child->student_id }}</td>
                            <td class="table-cell">{{ $child->user?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $child->grade_level ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="table-cell text-center text-gray-500 py-4">No children found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Fee Records</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Student</th>
                        <th class="table-header">Fee Structure</th>
                        <th class="table-header">Academic Year</th>
                        <th class="table-header">Amount</th>
                        <th class="table-header">Paid</th>
                        <th class="table-header">Balance</th>
                        <th class="table-header">Due Date</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Paid At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($feeRecords as $feeRecord)
                        <tr>
                            <td class="table-cell font-medium">{{ $feeRecord->student?->user?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $feeRecord->feeStructure?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $feeRecord->feeStructure?->academicYear?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ number_format((float) $feeRecord->amount, 2) }}</td>
                            <td class="table-cell">{{ number_format((float) $feeRecord->amount_paid, 2) }}</td>
                            <td class="table-cell font-medium">{{ number_format((float) $feeRecord->balance, 2) }}</td>
                            <td class="table-cell">{{ $feeRecord->due_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="status-badge {{ $feeRecord->status === 'paid' ? 'bg-green-100 text-green-800' : ($feeRecord->status === 'partial' ? 'bg-blue-100 text-blue-800' : ($feeRecord->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ ucfirst($feeRecord->status) }}
                                </span>
                            </td>
                            <td class="table-cell">{{ $feeRecord->paid_at?->format('M d, Y') ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="table-cell text-center text-gray-500 py-4">No fee records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
