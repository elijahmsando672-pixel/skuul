<x-layout :title="isset($title) ? trim((string) $title) : null">
    {{ $slot }}
</x-layout>
