<x-app-layout>
    <x-slot name="title">Fee Structure Details</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $fee->name }}</h1>
        <p class="text-gray-600 dark:text-slate-100">Fee structure details</p>
    </div>

    <div class="card max-w-2xl">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Academic Year</dt>
                <dd class="mt-1">{{ $fee->academicYear?->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Amount</dt>
                <dd class="mt-1">{{ number_format((float) $fee->amount, 2) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Frequency</dt>
                <dd class="mt-1">{{ ucfirst($fee->frequency) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Due Date</dt>
                <dd class="mt-1">{{ $fee->due_date?->format('M d, Y') ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Status</dt>
                <dd class="mt-1">{{ $fee->is_active ? 'Active' : 'Inactive' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Records</dt>
                <dd class="mt-1">{{ $fee->feeRecords()->count() }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.fees.edit', $fee) }}" class="btn-primary">Edit Fee Structure</a>
            <a href="{{ route('admin.fees.index') }}" class="btn-secondary">Back to Fee Structures</a>
        </div>
    </div>
</x-app-layout>
