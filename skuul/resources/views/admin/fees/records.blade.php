<x-app-layout>
    <x-slot name="title">Fee Records</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Fee Records</h1>
            <p class="text-gray-600">Manage student fee records and payments</p>
        </div>
        <a href="{{ route('admin.fees.index') }}" class="btn-secondary">Fee Structures</a>
    </div>

    <div class="card mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Create Fee Record</h2>
        <form method="POST" action="{{ route('admin.fees.records.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf
            <div>
                <label for="student_id" class="form-label">Student</label>
                <select id="student_id" name="student_id" class="input-field" required>
                    @forelse($students as $student)
                        <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>{{ $student->user?->name ?? 'N/A' }} ({{ $student->student_id }})</option>
                    @empty
                        <option value="" disabled selected>No students available</option>
                    @endforelse
                </select>
                @error('student_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="fee_structure_id" class="form-label">Fee Structure</label>
                <select id="fee_structure_id" name="fee_structure_id" class="input-field" required>
                    @forelse($feeStructures as $feeStructure)
                        <option value="{{ $feeStructure->id }}" @selected(old('fee_structure_id') == $feeStructure->id)>{{ $feeStructure->name }} - {{ $feeStructure->academicYear?->name ?? 'N/A' }}</option>
                    @empty
                        <option value="" disabled selected>No active fee structures available</option>
                    @endforelse
                </select>
                @error('fee_structure_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="amount" class="form-label">Amount</label>
                <input id="amount" type="number" name="amount" class="input-field" value="{{ old('amount') }}" step="0.01" min="0" required>
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="amount_paid" class="form-label">Amount Paid</label>
                <input id="amount_paid" type="number" name="amount_paid" class="input-field" value="{{ old('amount_paid', 0) }}" step="0.01" min="0">
                @error('amount_paid')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="due_date" class="form-label">Due Date</label>
                <input id="due_date" type="date" name="due_date" class="input-field" value="{{ old('due_date') }}" required>
                @error('due_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="input-field" required>
                    <option value="pending" @selected(old('status', 'pending') === 'pending')>Pending</option>
                    <option value="partial" @selected(old('status') === 'partial')>Partial</option>
                    <option value="paid" @selected(old('status') === 'paid')>Paid</option>
                    <option value="overdue" @selected(old('status') === 'overdue')>Overdue</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <button type="submit" class="btn-primary">Create Fee Record</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Student</th>
                        <th class="table-header">Fee Structure</th>
                        <th class="table-header">Amount</th>
                        <th class="table-header">Paid</th>
                        <th class="table-header">Balance</th>
                        <th class="table-header">Due Date</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Paid At</th>
                        <th class="table-header">Mark Payment</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($feeRecords as $feeRecord)
                        <tr>
                            <td class="table-cell font-medium">{{ $feeRecord->student?->user?->name ?? 'N/A' }}</td>
                            <td class="table-cell">
                                {{ $feeRecord->feeStructure?->name ?? 'N/A' }}
                                <span class="block text-xs text-gray-500">{{ $feeRecord->feeStructure?->academicYear?->name ?? '' }}</span>
                            </td>
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
                            <td class="table-cell">
                                <form method="POST" action="{{ route('admin.fees.records.status', $feeRecord) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="number" name="amount_paid" class="input-field w-28" value="{{ $feeRecord->balance }}" step="0.01" min="0" max="{{ $feeRecord->amount }}" required aria-label="Amount paid">
                                    <select name="status" class="input-field w-28" required aria-label="Status">
                                        <option value="pending" @selected($feeRecord->status === 'pending')>Pending</option>
                                        <option value="partial" @selected($feeRecord->status === 'partial')>Partial</option>
                                        <option value="paid" @selected($feeRecord->status === 'paid')>Paid</option>
                                        <option value="overdue" @selected($feeRecord->status === 'overdue')>Overdue</option>
                                    </select>
                                    <button type="submit" class="btn-secondary">Update</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="table-cell text-center text-gray-500 py-4">No fee records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $feeRecords->links() }}
    </div>
</x-app-layout>
