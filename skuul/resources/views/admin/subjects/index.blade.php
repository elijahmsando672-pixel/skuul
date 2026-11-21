<x-app-layout>
    <x-slot name="title">Subjects</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Subjects</h1>
            <p class="text-gray-600 dark:text-slate-100">Manage academic subjects</p>
        </div>
        <a href="{{ route('admin.subjects.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2"><path d="M12 4v16m8-8H4"/></svg>
            Add Subject
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Subject Code</th>
                        <th class="table-header">Name</th>
                        <th class="table-header">Classroom</th>
                        <th class="table-header">Teacher</th>
                        <th class="table-header">Credits</th>
                        <th class="table-header">Compulsory</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($subjects as $subject)
                        <tr>
                            <td class="table-cell font-medium">{{ $subject->code }}</td>
                            <td class="table-cell">{{ $subject->name }}</td>
                            <td class="table-cell">{{ $subject->classroom->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $subject->teacher?->user?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $subject->credits }}</td>
                            <td class="table-cell">{{ $subject->is_compulsory ? 'Yes' : 'No' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-cell text-center text-gray-500 dark:text-slate-100 py-4">No subjects found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $subjects->links() }}
    </div>
</x-app-layout>