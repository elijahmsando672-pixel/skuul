<x-app-layout>
    <x-slot name="title">Announcement Details</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $announcement->title }}</h1>
        <p class="text-gray-600 dark:text-slate-100">Announcement details</p>
    </div>

    <div class="card max-w-2xl">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Author</dt>
                <dd class="mt-1">{{ $announcement->user?->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Target Audience</dt>
                <dd class="mt-1">{{ ucfirst($announcement->target_audience) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Priority</dt>
                <dd class="mt-1">{{ ucfirst($announcement->priority) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Status</dt>
                <dd class="mt-1">{{ $announcement->is_published ? 'Published' : 'Draft' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Starts At</dt>
                <dd class="mt-1">{{ $announcement->starts_at?->format('M d, Y H:i') ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Expires At</dt>
                <dd class="mt-1">{{ $announcement->expires_at?->format('M d, Y H:i') ?? 'N/A' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-100">Content</dt>
                <dd class="mt-1 whitespace-pre-line">{{ $announcement->content }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn-primary">Edit Announcement</a>
            <a href="{{ route('admin.announcements.index') }}" class="btn-secondary">Back to Announcements</a>
        </div>
    </div>
</x-app-layout>
