<x-app-layout>
    <x-slot name="title">Library</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Library</h1>
        <p class="text-gray-600 dark:text-slate-100">Browse books and view your borrowings</p>
    </div>

    <div class="card mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">My Issued Books</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Book</th>
                        <th class="table-header">Issued</th>
                        <th class="table-header">Due</th>
                        <th class="table-header">Returned</th>
                        <th class="table-header">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($issuedBooks as $issue)
                        <tr>
                            <td class="table-cell font-medium">{{ $issue->book->title ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $issue->issued_at?->format('M d, Y') }}</td>
                            <td class="table-cell">{{ $issue->due_date?->format('M d, Y') }}</td>
                            <td class="table-cell">{{ $issue->returned_at?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="status-badge {{ $issue->is_overdue ? 'bg-red-100 text-red-800' : ($issue->status === 'returned' ? 'bg-gray-100 dark:bg-slate-800/60 text-gray-800 dark:text-slate-100' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ $issue->is_overdue ? 'Overdue' : ucfirst($issue->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-cell text-center text-gray-500 dark:text-slate-100 py-4">You have no issued books.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Available Books</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Title</th>
                        <th class="table-header">Author</th>
                        <th class="table-header">Category</th>
                        <th class="table-header">Shelf</th>
                        <th class="table-header">Availability</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($books as $item)
                        <tr>
                            <td class="table-cell font-medium">{{ $item->title }}</td>
                            <td class="table-cell">{{ $item->author }}</td>
                            <td class="table-cell">{{ $item->category }}</td>
                            <td class="table-cell">{{ $item->shelf_location ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="status-badge {{ $item->available_quantity > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $item->available_quantity > 0 ? $item->available_quantity . ' available' : 'Out of stock' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-cell text-center text-gray-500 dark:text-slate-100 py-4">No books found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $books->links() }}
    </div>
</x-app-layout>
