<x-app-layout>
    <x-slot name="title">Issued Books</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Issued Books</h1>
            <p class="text-gray-600">Issue and return library books</p>
        </div>
        <a href="{{ route('admin.library.index') }}" class="btn-secondary">Back to Library</a>
    </div>

    <div class="card mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Issue a Book</h2>
        <form method="POST" action="{{ route('admin.library.issues.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label for="book_id" class="form-label">Book</label>
                <select id="book_id" name="book_id" class="select-field" required>
                    @forelse($books as $item)
                        <option value="{{ $item->id }}" @selected(old('book_id') == $item->id)>{{ $item->title }} ({{ $item->available_quantity }} available)</option>
                    @empty
                        <option value="">No books available</option>
                    @endforelse
                </select>
            </div>
            <div>
                <label for="borrower_type" class="form-label">Borrower Type</label>
                <select id="borrower_type" name="borrower_type" class="select-field" required>
                    <option value="student" @selected(old('borrower_type') === 'student')>Student</option>
                    <option value="teacher" @selected(old('borrower_type') === 'teacher')>Teacher</option>
                </select>
            </div>
            <div>
                <label for="borrower_id" class="form-label">Borrower</label>
                <select id="borrower_id" name="borrower_id" class="select-field" required>
                    @forelse($students as $student)
                        <option value="{{ $student->id }}" @selected(old('borrower_id') == $student->id)>{{ $student->user->name }} (Student)</option>
                    @empty
                    @endforelse
                    @forelse($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected(old('borrower_id') == $teacher->id)>{{ $teacher->user->name }} (Teacher)</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div>
                <label for="issued_at" class="form-label">Issued Date</label>
                <input type="date" id="issued_at" name="issued_at" value="{{ old('issued_at', now()->toDateString()) }}" class="input-field" required>
            </div>
            <div>
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" id="due_date" name="due_date" value="{{ old('due_date', now()->addDays(14)->toDateString()) }}" class="input-field" required>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-primary w-full">Issue Book</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Book</th>
                        <th class="table-header">Borrower</th>
                        <th class="table-header">Issued</th>
                        <th class="table-header">Due</th>
                        <th class="table-header">Returned</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($issues as $issue)
                        <tr>
                            <td class="table-cell font-medium">{{ $issue->book->title ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $issue->borrower?->user->name ?? 'N/A' }} ({{ ucfirst($issue->borrower_type) }})</td>
                            <td class="table-cell">{{ $issue->issued_at?->format('M d, Y') }}</td>
                            <td class="table-cell">{{ $issue->due_date?->format('M d, Y') }}</td>
                            <td class="table-cell">{{ $issue->returned_at?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="status-badge {{ $issue->is_overdue ? 'bg-red-100 text-red-800' : ($issue->status === 'returned' ? 'bg-gray-100 text-gray-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ $issue->is_overdue ? 'Overdue' : ucfirst($issue->status) }}
                                </span>
                            </td>
                            <td class="table-cell">
                                @if($issue->returned_at === null)
                                    <form method="POST" action="{{ route('admin.library.issues.return', $issue) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-secondary">Return</button>
                                    </form>
                                @else
                                    <span class="text-gray-400">Returned</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-cell text-center text-gray-500 py-4">No issued books found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $issues->links() }}
    </div>
</x-app-layout>
