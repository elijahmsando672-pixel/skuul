<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) && trim((string) $title) !== '' ? trim((string) $title).' - ' : '' }}{{ config('app.name') }}</title>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-skuul-600 text-lg font-bold text-white">S</div>
                <h1 class="mt-4 text-2xl font-bold tracking-tight">{{ config('app.name') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">School management, simplified.</p>
            </div>

            <div class="card">
                @if (isset($title) && trim((string) $title) !== '')
                    <h2 class="mb-6 text-xl font-semibold">{{ $title }}</h2>
                @endif

                @if (session('status'))
                    <div class="mb-4 rounded-lg border-l-4 border-emerald-500 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg border-l-4 border-red-500 bg-red-50 px-4 py-3 text-sm text-red-800 dark:bg-red-950/60 dark:text-red-200">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </div>

            <p class="mt-6 text-center text-xs text-gray-400 dark:text-slate-500">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
