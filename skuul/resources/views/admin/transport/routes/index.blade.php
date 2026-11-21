<x-app-layout>
    <x-slot name="title">Transport Routes</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Transport Routes</h1>
            <p class="text-gray-600">Manage bus routes, pickup points, and travel times</p>
        </div>
        <a href="{{ route('admin.transport.routes.create') }}" class="btn-primary">Add Route</a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Name</th>
                        <th class="table-header">Origin</th>
                        <th class="table-header">Destination</th>
                        <th class="table-header">Distance (km)</th>
                        <th class="table-header">Estimated Minutes</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($routes as $route)
                        <tr>
                            <td class="table-cell font-medium">{{ $route->name }}</td>
                            <td class="table-cell">{{ $route->origin }}</td>
                            <td class="table-cell">{{ $route->destination }}</td>
                            <td class="table-cell">{{ $route->distance_km ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $route->estimated_minutes ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="status-badge">{{ $route->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="table-cell">
                                <a href="{{ route('admin.transport.routes.edit', $route) }}" class="text-skuul-600 hover:text-skuul-500">Edit</a>
                                <form method="POST" action="{{ route('admin.transport.routes.destroy', $route) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger ml-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-cell text-center text-gray-500 py-4">No transport routes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $routes->links() }}
    </div>
</x-app-layout>
