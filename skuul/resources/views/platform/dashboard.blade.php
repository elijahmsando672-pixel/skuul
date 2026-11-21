<x-app-layout>
    <x-slot name="title">Platform Dashboard</x-slot>
    <div class="grid md:grid-cols-4 gap-4 mb-6">
        @foreach([['Total Schools',$stats['total_schools']],['Active',$stats['active_schools']],['Pending',$stats['pending_schools']],['Suspended',$stats['suspended_schools']],['Students',$stats['total_students']],['Teachers',$stats['total_teachers']],['Active Subs',$stats['active_subscriptions']],['MRR (KES)',number_format($stats['mrr'],2)]] as $card)
            <div class="card"><div class="text-sm text-gray-500">{{ $card[0] }}</div><div class="text-2xl font-bold">{{ $card[1] }}</div></div>
        @endforeach
    </div>
    <div class="grid md:grid-cols-2 gap-6">
        <div class="card"><h3 class="font-semibold mb-3">Recent Schools</h3>
            <ul class="space-y-2">@foreach($recentSchools as $s)<li class="flex justify-between"><span>{{ $s->name }}</span><span class="text-sm">{{ $s->status }}</span></li>@endforeach</ul>
        </div>
        <div class="card"><h3 class="font-semibold mb-3">Recent Audit</h3>
            <ul class="space-y-2 text-sm">@foreach($recentAudits as $a)<li>{{ $a->action }} - {{ $a->school?->name }} - {{ $a->created_at?->diffForHumans() }}</li>@endforeach</ul>
        </div>
    </div>
</x-app-layout>
