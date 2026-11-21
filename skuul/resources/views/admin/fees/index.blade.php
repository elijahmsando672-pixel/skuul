<x-app-layout>
    <x-slot name="title">Fee Structures</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Fee Structures</h1>
            <p class="text-gray-600">Manage fee structures and student fee records</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.fees.records') }}" class="btn-secondary">View Records</a>
            <a href="{{ route('admin.fees.create') }}" class="btn-primary">Add Fee Structure</a>
        </div>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Name</th>
                        <th class="table-header">Academic Year</th>
                        <th class="table-header">Amount</th>
                        <th class="table-header">Frequency</th>
                        <th class="table-header">Due Date</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Records</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($feeStructures as $feeStructure)
                        <tr>
                            <td class="table-cell font-medium">{{ $feeStructure->name }}</td>
                            <td class="table-cell">{{ $feeStructure->academicYear?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ number_format((float) $feeStructure->amount, 2) }}</td>
                            <td class="table-cell">{{ ucfirst($feeStructure->frequency) }}</td>
                            <td class="table-cell">{{ $feeStructure->due_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="status-badge {{ $feeStructure->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $feeStructure->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="table-cell">{{ $feeStructure->fee_records_count }}</td>
                            <td class="table-cell">
                                <a href="{{ route('admin.fees.edit', $feeStructure) }}" class="text-skuul-600 hover:text-skuul-500">Edit</a>
                                <form method="POST" action="{{ route('admin.fees.destroy', $feeStructure) }}" class="inline" onsubmit="return confirm('Delete this fee structure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger ml-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="table-cell text-center text-gray-500 py-4">No fee structures found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $feeStructures->links() }}
    </div>
</x-app-layout>
