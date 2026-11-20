<x-app-layout>
    <x-slot name="title">Academic Years</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Academic Years</h1>
            <p class="text-gray-600">Manage academic years</p>
        </div>
        <a href="{{ route('admin.academic-years.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2"><path d="M12 4v16m8-8H4"/></svg>
            Add Academic Year
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Name</th>
                        <th class="table-header">Start Date</th>
                        <th class="table-header">End Date</th>
                        <th class="table-header">Active</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($years as $year)
                        <tr>
                            <td class="table-cell">{{ $year->name }}</td>
                            <td class="table-cell">{{ $year->start_date->format('M d, Y') }}</td>
                            <td class="table-cell">{{ $year->end_date->format('M d, Y') }}</td>
                            <td class="table-cell">{{ $year->is_active ? 'Yes' : 'No' }}</td>
                            <td class="table-cell">
                                <a href="{{ route('admin.academic-years.show', $year) }}" class="text-skuul-600 hover:text-skuul-500">View</a>
                                <a href="{{ route('admin.academic-years.edit', $year) }}" class="ml-2 text-gray-500 hover:text-gray-700">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-cell text-center text-gray-500 py-4">No academic years found.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>