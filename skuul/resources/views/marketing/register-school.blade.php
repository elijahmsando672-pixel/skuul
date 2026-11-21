<x-guest-layout>
    <h2 class="text-2xl font-bold mb-4">Register Your School</h2>
    <form method="POST" action="{{ route('school.register.store') }}" class="space-y-4">
        @csrf
        <div class="grid md:grid-cols-2 gap-4">
            <input class="input-field" name="school_name" placeholder="School name" value="{{ old('school_name') }}" required>
            <input class="input-field" name="school_type" placeholder="School type" value="{{ old('school_type') }}">
            <input class="input-field" name="registration_number" placeholder="Registration number" value="{{ old('registration_number') }}">
            <input class="input-field" name="country" placeholder="Country" value="{{ old('country','Kenya') }}" required>
            <input class="input-field" name="county" placeholder="County" value="{{ old('county') }}">
            <input class="input-field" name="phone" placeholder="Phone" value="{{ old('phone') }}">
        </div>
        <input class="input-field" name="address" placeholder="Physical address" value="{{ old('address') }}">
        <input class="input-field" type="email" name="contact_email" placeholder="School contact email" value="{{ old('contact_email') }}" required>
        <hr>
        <div class="grid md:grid-cols-2 gap-4">
            <input class="input-field" name="admin_name" placeholder="Administrator name" value="{{ old('admin_name') }}" required>
            <input class="input-field" type="email" name="admin_email" placeholder="Administrator email" value="{{ old('admin_email') }}" required>
            <input class="input-field" type="password" name="admin_password" placeholder="Password" required>
            <input class="input-field" type="password" name="admin_password_confirmation" placeholder="Confirm password" required>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <select class="select-field" name="plan_id" required>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" @selected(old('plan_id')==$plan->id)>{{ $plan->name }} - KSh {{ number_format($plan->monthly_price,2) }}/mo</option>
                @endforeach
            </select>
            <select class="select-field" name="billing_cycle" required>
                <option value="monthly">Monthly</option>
                <option value="annual">Annual</option>
            </select>
        </div>
        <button class="btn-primary w-full">Submit Registration</button>
    </form>
</x-guest-layout>
