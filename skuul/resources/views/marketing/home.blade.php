<x-guest-layout>
    <div class="space-y-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-slate-100">SKUUL School Management SaaS</h1>
            <p class="mt-3 text-gray-600 dark:text-slate-300">Multi-tenant platform for Kenyan schools: academics, finance, communication, and operations.</p>
            <div class="mt-6 flex gap-3 justify-center">
                <a href="{{ route('school.register') }}" class="btn-primary">Register Your School</a>
                <a href="{{ route('login') }}" class="btn-secondary">Sign In</a>
            </div>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            @foreach($plans as $plan)
                <div class="card">
                    <h3 class="font-semibold text-lg">{{ $plan->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $plan->description }}</p>
                    <p class="mt-3 text-2xl font-bold">KSh {{ number_format($plan->monthly_price,2) }}<span class="text-sm font-normal">/mo</span></p>
                    <p class="text-sm text-gray-500">or KSh {{ number_format($plan->annual_price,2) }}/yr</p>
                </div>
            @endforeach
        </div>
    </div>
</x-guest-layout>
