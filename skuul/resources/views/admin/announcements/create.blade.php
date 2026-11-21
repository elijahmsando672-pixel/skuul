<x-app-layout>
    <x-slot name="title">Create Announcement</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Create Announcement</h2>
            <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="title" class="form-label">Title</label>
                    <input id="title" type="text" name="title" class="input-field" value="{{ old('title') }}" required>
                </div>
                <div>
                    <label for="content" class="form-label">Content</label>
                    <textarea id="content" name="content" class="input-field" rows="5" required>{{ old('content') }}</textarea>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="target_audience" class="form-label">Target Audience</label>
                        <select id="target_audience" name="target_audience" class="input-field" required>
                            @foreach(['all' => 'All', 'students' => 'Students', 'teachers' => 'Teachers', 'parents' => 'Parents', 'admin' => 'Admin'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('target_audience', 'all') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="form-label">Priority</label>
                        <select id="priority" name="priority" class="input-field" required>
                            @foreach(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('priority', 'normal') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="starts_at" class="form-label">Starts At</label>
                        <input id="starts_at" type="datetime-local" name="starts_at" class="input-field" value="{{ old('starts_at') }}">
                    </div>
                    <div>
                        <label for="expires_at" class="form-label">Expires At</label>
                        <input id="expires_at" type="datetime-local" name="expires_at" class="input-field" value="{{ old('expires_at') }}">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_published" value="0">
                    <input id="is_published" type="checkbox" name="is_published" value="1" class="rounded border-gray-300" @checked(old('is_published', false))>
                    <label for="is_published" class="form-label mb-0">Published</label>
                </div>
                <button type="submit" class="btn-primary w-full">Create Announcement</button>
            </form>
        </div>
        <a href="{{ route('admin.announcements.index') }}" class="btn-secondary w-full">Back to Announcements</a>
    </div>
</x-app-layout>
