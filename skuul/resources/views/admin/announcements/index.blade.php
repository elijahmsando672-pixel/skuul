<x-app-layout>
    <x-slot name="title">Announcements</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Announcements</h1>
            <p class="text-gray-600 dark:text-slate-100">School-wide announcements</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2"><path d="M12 4v16m8-8H4"/></svg>
            New Announcement
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Title</th>
                        <th class="table-header">Content</th>
                        <th class="table-header">Audience</th>
                        <th class="table-header">Priority</th>
                        <th class="table-header">Published</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($announcements as $announcement)
                        <tr>
                            <td class="table-cell font-medium">{{ $announcement->title }}</td>
                            <td class="table-cell">{{ Str::limit($announcement->content, 50) }}</td>
                            <td class="table-cell">{{ ucfirst($announcement->target_audience) }}</td>
                            <td class="table-cell">
                                <span class="status-badge {{ $announcement->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($announcement->priority === 'high' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 dark:bg-slate-800/60 text-gray-800 dark:text-slate-100') }}">
                                    {{ ucfirst($announcement->priority) }}
                                </span>
                            </td>
                            <td class="table-cell">{{ $announcement->is_published ? 'Yes' : 'No' }}</td>
                            <td class="table-cell">
                                <a href="{{ route('admin.announcements.show', $announcement) }}" class="text-skuul-600 hover:text-skuul-500">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-cell text-center text-gray-500 dark:text-slate-100 py-4">No announcements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $announcements->links() }}
    </div>
</x-app-layout>