<x-app-layout>
    <x-slot name="title">Transport Assignments</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Transport Assignments</h1>
        <p class="text-gray-600">Assign students and teachers to routes and vehicles</p>
    </div>

    <div class="card mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Add Assignment</h2>
        <form method="POST" action="{{ route('admin.transport.assignments.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label for="assignable_type" class="form-label">Type</label>
                <select id="assignable_type" name="assignable_type" class="select-field" required>
                    <option value="student" @selected(old('assignable_type', 'student') === 'student')>Student</option>
                    <option value="teacher" @selected(old('assignable_type') === 'teacher')>Teacher</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="assignable_id" class="form-label">Student or Teacher</label>
                <select id="assignable_id" name="assignable_id" class="select-field" required>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" data-type="student" @selected(old('assignable_type', 'student') === 'student' && old('assignable_id') == $student->id)>{{ $student->user?->name ?? 'Student #'.$student->id }}</option>
                    @endforeach
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" data-type="teacher" @selected(old('assignable_type') === 'teacher' && old('assignable_id') == $teacher->id)>{{ $teacher->user?->name ?? 'Teacher #'.$teacher->id }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="transport_route_id" class="form-label">Route</label>
                <select id="transport_route_id" name="transport_route_id" class="select-field" required>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" @selected(old('transport_route_id') == $route->id)>{{ $route->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="vehicle_id" class="form-label">Vehicle</label>
                <select id="vehicle_id" name="vehicle_id" class="select-field" required>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>{{ $vehicle->registration_number }} ({{ $vehicle->make }} {{ $vehicle->model }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="pickup_point" class="form-label">Pickup Point</label>
                <input id="pickup_point" type="text" name="pickup_point" value="{{ old('pickup_point') }}" class="input-field">
            </div>
            <div>
                <label for="academic_year_id" class="form-label">Academic Year</label>
                <select id="academic_year_id" name="academic_year_id" class="select-field">
                    <option value="">No academic year</option>
                    @foreach($academicYears as $academicYear)
                        <option value="{{ $academicYear->id }}" @selected(old('academic_year_id') == $academicYear->id)>{{ $academicYear->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2 pb-2">
                <input type="hidden" name="is_active" value="0">
                <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-skuul-600" @checked(old('is_active', true))>
                <label for="is_active" class="form-label mb-0">Active</label>
            </div>
            <div>
                <button type="submit" class="btn-primary w-full">Add Assignment</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Person</th>
                        <th class="table-header">Route</th>
                        <th class="table-header">Vehicle</th>
                        <th class="table-header">Pickup Point</th>
                        <th class="table-header">Academic Year</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($assignments as $assignment)
                        <tr>
                            <td class="table-cell font-medium">
                                {{ $assignment->assignable?->user?->name ?? 'N/A' }}
                                <span class="block text-xs text-gray-500">{{ ucfirst($assignment->assignable_type) }}</span>
                            </td>
                            <td class="table-cell">{{ $assignment->transportRoute->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $assignment->vehicle->registration_number ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $assignment->pickup_point ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $assignment->academicYear->name ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="status-badge">{{ $assignment->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="table-cell">
                                <form method="POST" action="{{ route('admin.transport.assignments.destroy', $assignment) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-cell text-center text-gray-500 py-4">No transport assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $assignments->links() }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('assignable_type');
            const assignableSelect = document.getElementById('assignable_id');
            const options = Array.from(assignableSelect.options);

            const updateOptions = function () {
                options.forEach(function (option) {
                    option.hidden = option.dataset.type !== typeSelect.value;
                    option.disabled = option.hidden;
                });

                const firstAvailable = assignableSelect.querySelector('option:not([disabled])');
                if (firstAvailable) {
                    assignableSelect.value = firstAvailable.value;
                }
            };

            typeSelect.addEventListener('change', updateOptions);
            updateOptions();
        });
    </script>
</x-app-layout>
