<x-app-layout>
    <x-slot name="title">Edit Transport Route</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Edit Transport Route</h2>
            <form method="POST" action="{{ route('admin.transport.routes.update', $route) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="form-label">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $route->name) }}" class="input-field" required>
                </div>
                <div>
                    <label for="origin" class="form-label">Origin</label>
                    <input id="origin" type="text" name="origin" value="{{ old('origin', $route->origin) }}" class="input-field" required>
                </div>
                <div>
                    <label for="destination" class="form-label">Destination</label>
                    <input id="destination" type="text" name="destination" value="{{ old('destination', $route->destination) }}" class="input-field" required>
                </div>
                <div>
                    <label for="distance_km" class="form-label">Distance (km)</label>
                    <input id="distance_km" type="number" step="0.01" min="0" name="distance_km" value="{{ old('distance_km', $route->distance_km) }}" class="input-field">
                </div>
                <div>
                    <label for="estimated_minutes" class="form-label">Estimated Minutes</label>
                    <input id="estimated_minutes" type="number" min="0" name="estimated_minutes" value="{{ old('estimated_minutes', $route->estimated_minutes) }}" class="input-field">
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-skuul-600" @checked(old('is_active', $route->is_active))>
                    <label for="is_active" class="form-label mb-0">Active</label>
                </div>
                <button type="submit" class="btn-primary w-full">Update Route</button>
            </form>
        </div>
        <a href="{{ route('admin.transport.routes.index') }}" class="btn-secondary w-full">Back to Routes</a>
    </div>
</x-app-layout>
