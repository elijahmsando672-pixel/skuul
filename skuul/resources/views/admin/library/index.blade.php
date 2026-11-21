<x-app-layout>
    <x-slot name="title">Library</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Library</h1>
            <p class="text-gray-600">Manage books and borrowing records</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.library.issues') }}" class="btn-secondary">Issued Books</a>
            <a href="{{ route('admin.library.create') }}" class="btn-primary">Add Book</a>
        </div>
    </div>

    @isset($book)
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">{{ $book->title }}</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                <div>
                    <dt class="text-gray-500">Author</dt>
                    <dd class="font-medium">{{ $book->author }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">ISBN</dt>
                    <dd class="font-medium">{{ $book->isbn ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Shelf</dt>
                    <dd class="font-medium">{{ $book->shelf_location ?? 'N/A' }}</dd>
                </div>
            </dl>
            @forelse($book->issues as $issue)
                @if ($loop->first)
                    <h3 class="text-md font-semibold text-gray-900 mt-4 mb-2">Borrowing history</h3>
                    <ul class="space-y-1 text-sm">
                @endif
                <li>
                    {{ $issue->borrower?->user->name ?? 'Unknown borrower' }}
                    <span class="status-badge {{ $issue->is_overdue ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $issue->is_overdue ? 'Overdue' : ucfirst($issue->status) }}
                    </span>
                </li>
                @if ($loop->last)
                    </ul>
                @endif
            @empty
                <p class="text-sm text-gray-500 mt-4">No borrowing history for this book.</p>
            @endforelse
        </div>
    @endisset

    <form method="GET" action="{{ route('admin.library.index') }}" class="card mb-6 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-64">
            <label for="search" class="form-label">Search books</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" class="input-field" placeholder="Title, author, ISBN, or category">
        </div>
        <button type="submit" class="btn-secondary">Search</button>
    </form>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Title</th>
                        <th class="table-header">Author</th>
                        <th class="table-header">Category</th>
                        <th class="table-header">ISBN</th>
                        <th class="table-header">Copies</th>
                        <th class="table-header">Availability</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($books as $item)
                        <tr>
                            <td class="table-cell font-medium">{{ $item->title }}</td>
                            <td class="table-cell">{{ $item->author }}</td>
                            <td class="table-cell">{{ $item->category }}</td>
                            <td class="table-cell">{{ $item->isbn ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $item->available_quantity }} / {{ $item->quantity }}</td>
                            <td class="table-cell">
                                <span class="status-badge {{ $item->available_quantity > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $item->available_quantity > 0 ? 'Available' : 'Out of stock' }}
                                </span>
                            </td>
                            <td class="table-cell">
                                <a href="{{ route('admin.library.show', $item) }}" class="text-skuul-600 hover:text-skuul-500">View</a>
                                <a href="{{ route('admin.library.edit', $item) }}" class="ml-2 text-gray-500 hover:text-gray-700">Edit</a>
                                <form method="POST" action="{{ route('admin.library.destroy', $item) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger ml-2" onclick="return confirm('Delete this book?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-cell text-center text-gray-500 py-4">No books found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $books->links() }}
    </div>
</x-app-layout>
