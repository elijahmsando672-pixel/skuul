<!DOCTYPE html>
<html lang="en" class="h-full">
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
<body class="h-full bg-gray-50 text-gray-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-900/50 lg:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transform bg-slate-900 text-slate-100 transition-transform duration-200 lg:translate-x-0 dark:bg-slate-900">
        <div class="flex h-16 items-center justify-between border-b border-slate-800 px-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 font-semibold">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-skuul-600 text-white">S</span>
                <span class="truncate">SKUUL</span>
            </a>
            <button type="button" id="sidebar-close" class="rounded-md p-1.5 text-slate-300 hover:bg-slate-800 lg:hidden" aria-label="Close navigation">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <nav class="h-[calc(100%-4rem)] overflow-y-auto px-3 py-4 text-sm">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">Dashboard</a>

            @if (auth()->user()->hasRole('admin'))
                <p class="sidebar-section">Administration</p>
                <a href="{{ url('/admin/users') }}" class="sidebar-link">Users</a>
                <a href="{{ url('/admin/students') }}" class="sidebar-link">Students</a>
                <a href="{{ url('/admin/teachers') }}" class="sidebar-link">Teachers</a>
                <a href="{{ url('/admin/classrooms') }}" class="sidebar-link">Classrooms</a>
                <a href="{{ url('/admin/subjects') }}" class="sidebar-link">Subjects</a>
                <a href="{{ url('/admin/academic-years') }}" class="sidebar-link">Academic Years</a>
                <a href="{{ url('/admin/announcements') }}" class="sidebar-link">Announcements</a>

                <p class="sidebar-section">School Operations</p>
                <a href="{{ url('/admin/timetables') }}" class="sidebar-link">Timetables</a>
                <a href="{{ url('/admin/fees') }}" class="sidebar-link">Fees</a>
                <a href="{{ url('/admin/fees/records') }}" class="sidebar-link">Fee Records</a>
                <a href="{{ url('/admin/exams') }}" class="sidebar-link">Examinations</a>
                <a href="{{ url('/admin/library') }}" class="sidebar-link">Library</a>
                <a href="{{ url('/admin/transport/routes') }}" class="sidebar-link">Transport Routes</a>
                <a href="{{ url('/admin/transport/vehicles') }}" class="sidebar-link">Vehicles</a>
                <a href="{{ url('/admin/transport/assignments') }}" class="sidebar-link">Assignments</a>
                <a href="{{ url('/admin/notifications') }}" class="sidebar-link">Notifications</a>
            @endif

            @if (auth()->user()->hasRole('teacher'))
                <p class="sidebar-section">Teaching</p>
                <a href="{{ url('/teacher/attendance') }}" class="sidebar-link">Attendance</a>
                <a href="{{ url('/teacher/grades') }}" class="sidebar-link">Grades</a>
                <a href="{{ url('/teacher/timetable') }}" class="sidebar-link">My Timetable</a>
                <a href="{{ url('/teacher/exams') }}" class="sidebar-link">Examinations</a>
                <a href="{{ url('/teacher/library') }}" class="sidebar-link">Library</a>
            @endif

            @if (auth()->user()->hasRole('student'))
                <p class="sidebar-section">Academics</p>
                <a href="{{ url('/student/attendance') }}" class="sidebar-link">Attendance</a>
                <a href="{{ url('/student/grades') }}" class="sidebar-link">Grades</a>
                <a href="{{ url('/student/timetable') }}" class="sidebar-link">Timetable</a>
                <a href="{{ url('/student/exams') }}" class="sidebar-link">Examinations</a>
                <a href="{{ url('/student/library') }}" class="sidebar-link">Library</a>
                <a href="{{ url('/student/transport') }}" class="sidebar-link">Transport</a>
            @endif

            @if (auth()->user()->hasRole('parent'))
                <p class="sidebar-section">Child Portal</p>
                <a href="{{ url('/parent/fees') }}" class="sidebar-link">Fees</a>
                <a href="{{ url('/parent/transport') }}" class="sidebar-link">Transport</a>
            @endif
        </nav>
    </aside>

    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-gray-200 bg-white/90 px-4 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90 sm:px-6">
            <div class="flex items-center gap-3">
                <button type="button" id="sidebar-toggle" class="rounded-md p-2 text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-800 lg:hidden" aria-label="Open navigation">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <h1 class="truncate text-lg font-semibold">{{ $title ?? config('app.name') }}</h1>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" id="theme-toggle" class="rounded-md p-2 text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-800" aria-label="Toggle dark mode">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.36 6.36l-.7-.7M6.34 6.34l-.7.7M12 8a4 4 0 100 8 4 4 0 000-8z" /></svg>
                </button>
                @if (session('impersonator_id'))
                    <span class="hidden text-sm font-medium text-amber-700 dark:text-amber-300 sm:inline">Viewing as {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.impersonate.leave') }}">
                        @csrf
                        <button type="submit" class="rounded-md bg-amber-100 px-3 py-2 text-sm font-medium text-amber-800 hover:bg-amber-200 dark:bg-amber-900/50 dark:text-amber-100 dark:hover:bg-amber-900">Return to Admin</button>
                    </form>
                @endif
                <span class="hidden text-sm text-gray-600 dark:text-slate-300 sm:inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800">Log out</button>
                </form>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @foreach (['success' => 'border-emerald-500 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-200', 'error' => 'border-red-500 bg-red-50 text-red-800 dark:bg-red-950/60 dark:text-red-200', 'warning' => 'border-amber-500 bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-200'] as $key => $classes)
                @if (session($key))
                    <div class="flash-message mb-4 rounded-lg border-l-4 px-4 py-3 text-sm {{ $classes }}" role="alert">
                        {{ session($key) }}
                    </div>
                @endif
            @endforeach

            @if ($errors->any())
                <div class="flash-message mb-4 rounded-lg border-l-4 border-red-500 bg-red-50 px-4 py-3 text-sm text-red-800 dark:bg-red-950/60 dark:text-red-200" role="alert">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>
</html>
