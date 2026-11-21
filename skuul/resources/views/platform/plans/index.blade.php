<x-app-layout>
    <x-slot name="title">Plans</x-slot>
    <div class="card mb-6">
        <form method="POST" action="{{ route('platform.plans.store') }}" class="grid md:grid-cols-4 gap-3">
            @csrf
            <input class="input-field" name="name" placeholder="Name" required>
            <input class="input-field" name="monthly_price" type="number" step="0.01" placeholder="Monthly" required>
            <input class="input-field" name="annual_price" type="number" step="0.01" placeholder="Annual" required>
            <input class="input-field" name="currency" value="KES" required>
            <input class="input-field md:col-span-2" name="description" placeholder="Description">
            <input class="input-field" name="trial_days" type="number" value="14" required>
            <button class="btn-primary">Create Plan</button>
        </form>
    </div>
    <div class="card overflow-x-auto"><table class="min-w-full"><thead><tr><th class="table-header">Name</th><th class="table-header">Monthly</th><th class="table-header">Annual</th><th class="table-header">Active</th></tr></thead><tbody>
    @foreach($plans as $plan)<tr><td class="table-cell">{{ $plan->name }}</td><td class="table-cell">{{ $plan->monthly_price }}</td><td class="table-cell">{{ $plan->annual_price }}</td><td class="table-cell">{{ $plan->is_active ? 'Yes':'No' }}</td></tr>@endforeach
    </tbody></table></div>
</x-app-layout>
