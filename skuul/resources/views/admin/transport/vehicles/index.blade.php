<x-app-layout>
    <x-slot name="title">Transport Vehicles</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Transport Vehicles</h1>
        <p class="text-gray-600">Manage vehicles, capacity, and driver details</p>
    </div>

    <div class="card mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Add Vehicle</h2>
        <form method="POST" action="{{ route('admin.transport.vehicles.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="form-label">Registration Number</label>
                <input type="text" name="registration_number" value="{{ old('registration_number') }}" class="input-field" required>
            </div>
            <div>
                <label class="form-label">Make</label>
                <input type="text" name="make" value="{{ old('make') }}" class="input-field" required>
            </div>
            <div>
                <label class="form-label">Model</label>
                <input type="text" name="model" value="{{ old('model') }}" class="input-field" required>
            </div>
            <div>
                <label class="form-label">Capacity</label>
                <input type="number" min="1" name="capacity" value="{{ old('capacity') }}" class="input-field" required>
            </div>
            <div>
                <label class="form-label">Driver Name</label>
                <input type="text" name="driver_name" value="{{ old('driver_name') }}" class="input-field" required>
            </div>
            <div>
                <label class="form-label">Driver Phone</label>
                <input type="text" name="driver_phone" value="{{ old('driver_phone') }}" class="input-field">
            </div>
            <div class="flex items-center gap-2 pb-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-skuul-600" @checked(old('is_active', true))>
                <label class="form-label mb-0">Active</label>
            </div>
            <div>
                <button type="submit" class="btn-primary w-full">Add Vehicle</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Registration</th>
                        <th class="table-header">Make</th>
                        <th class="table-header">Model</th>
                        <th class="table-header">Capacity</th>
                        <th class="table-header">Driver</th>
                        <th class="table-header">Phone</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($vehicles as $vehicle)
                        <tr>
                            <td class="table-cell font-medium">{{ $vehicle->registration_number }}</td>
                            <td class="table-cell">{{ $vehicle->make }}</td>
                            <td class="table-cell">{{ $vehicle->model }}</td>
                            <td class="table-cell">{{ $vehicle->capacity }}</td>
                            <td class="table-cell">{{ $vehicle->driver_name }}</td>
                            <td class="table-cell">{{ $vehicle->driver_phone ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="status-badge">{{ $vehicle->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="table-cell">
                                <form method="POST" action="{{ route('admin.transport.vehicles.destroy', $vehicle) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td colspan="8" class="table-cell">
                                <form method="POST" action="{{ route('admin.transport.vehicles.update', $vehicle) }}" class="grid grid-cols-1 md:grid-cols-8 gap-3 items-end">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="form-label">Registration</label>
                                        <input type="text" name="registration_number" value="{{ old('registration_number', $vehicle->registration_number) }}" class="input-field" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Make</label>
                                        <input type="text" name="make" value="{{ old('make', $vehicle->make) }}" class="input-field" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Model</label>
                                        <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" class="input-field" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Capacity</label>
                                        <input type="number" min="1" name="capacity" value="{{ old('capacity', $vehicle->capacity) }}" class="input-field" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Driver</label>
                                        <input type="text" name="driver_name" value="{{ old('driver_name', $vehicle->driver_name) }}" class="input-field" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="driver_phone" value="{{ old('driver_phone', $vehicle->driver_phone) }}" class="input-field">
                                    </div>
                                    <div class="flex items-center gap-2 pb-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-skuul-600" @checked(old('is_active', $vehicle->is_active))>
                                        <label class="form-label mb-0">Active</label>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn-primary w-full">Save</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="table-cell text-center text-gray-500 py-4">No vehicles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $vehicles->links() }}
    </div>
</x-app-layout>
