<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Dashboard</h1>
        <p class="text-gray-600 dark:text-slate-100">Welcome back to SKUUL Management System</p>
    </div>

    @if($stats)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            @foreach($stats as $key => $value)
                <div class="card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-slate-100 uppercase tracking-wider">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $value }}</p>
                        </div>
                        <div class="w-10 h-10 bg-skuul-100 rounded-lg flex items-center justify-center text-skuul-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100">Recent Announcements</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                <thead class="bg-gray-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="table-header">Title</th>
                        <th class="table-header">Audience</th>
                        <th class="table-header">Priority</th>
                        <th class="table-header">Published</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse($announcements as $announcement)
                        <tr>
                            <td class="table-cell">{{ $announcement->title }}</td>
                            <td class="table-cell">{{ ucfirst($announcement->target_audience) }}</td>
                            <td class="table-cell">
                                <span class="status-badge {{ $announcement->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($announcement->priority === 'high' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 dark:bg-slate-800/60 text-gray-800 dark:text-slate-100') }}">
                                    {{ ucfirst($announcement->priority) }}
                                </span>
                            </td>
                            <td class="table-cell">{{ $announcement->is_published ? 'Yes' : 'No' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="table-cell text-center text-gray-500 dark:text-slate-100 py-4">No announcements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>