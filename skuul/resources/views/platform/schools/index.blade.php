<x-app-layout>
    <x-slot name="title">Schools</x-slot>
    <div class="card overflow-x-auto">
        <table class="min-w-full"><thead><tr><th class="table-header">Name</th><th class="table-header">Status</th><th class="table-header">Users</th><th class="table-header">Actions</th></tr></thead>
        <tbody>@foreach($schools as $school)
            <tr><td class="table-cell">{{ $school->name }}</td><td class="table-cell">{{ $school->status }}</td><td class="table-cell">{{ $school->users_count }}</td>
            <td class="table-cell space-x-2">
                <a href="{{ route('platform.schools.show',$school) }}" class="text-skuul-600">View</a>
                <form method="POST" action="{{ route('platform.schools.approve',$school) }}" class="inline">@csrf<button class="text-green-600">Approve</button></form>
                <form method="POST" action="{{ route('platform.schools.suspend',$school) }}" class="inline">@csrf<input type="hidden" name="reason" value="Suspended by platform admin"><button class="text-red-600">Suspend</button></form>
            </td></tr>
        @endforeach</tbody></table>
        <div class="p-4">{{ $schools->links() }}</div>
    </div>
</x-app-layout>
