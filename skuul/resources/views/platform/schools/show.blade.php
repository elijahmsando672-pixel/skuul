<x-app-layout>
    <x-slot name="title">School Details</x-slot>
    <div class="card space-y-2">
        <div><strong>Name:</strong> {{ $school->name }}</div>
        <div><strong>Status:</strong> {{ $school->status }}</div>
        <div><strong>Contact:</strong> {{ $school->contact_email }} / {{ $school->phone }}</div>
        <div><strong>Address:</strong> {{ $school->address }}, {{ $school->county }}, {{ $school->country }}</div>
        <div><strong>Users:</strong> {{ $school->users->count() }}</div>
    </div>
</x-app-layout>
