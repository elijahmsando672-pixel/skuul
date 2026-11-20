<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">

    @if(session('status'))
        <div class="flash-message fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('status') }}
        </div>
    @endif

    <nav class="bg-white shadow-sm border-b border-gray-100 fixed top-0 w-full z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <div class="w-8 h-8 bg-skuul-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">S</div>
                        <span class="font-bold text-xl text-gray-800 tracking-tight">SKUUL</span>
                    </div>
                    <button id="sidebar-toggle" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <div class="ml-3 relative">
                            <button class="flex items-center gap-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                                <img class="w-8 h-8 rounded-full border border-gray-200" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0ea5e9&color=fff">
                                <svg class="w-4 h-4 ml-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1">
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">Sign in</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="flex pt-16">
        <aside id="sidebar" class="hidden lg:flex fixed left-0 top-16 h-full w-64 bg-white border-r border-gray-100 z-50">
            <div class="p-4">
                <div class="mb-6 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    Navigation
                </div>
                <nav class="space-y-1">
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">
                            <svg class="w-5 h-5"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Dashboard
                        </a>
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Users</a>
                            <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Students</a>
                            <a href="{{ route('admin.teachers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Teachers</a>
                            <a href="{{ route('admin.classrooms.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Classrooms</a>
                            <a href="{{ route('admin.subjects.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Subjects</a>
                            <a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Announcements</a>
                        @endif
                        @if(auth()->user()->hasRole('teacher'))
                            <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">My Dashboard</a>
                            <a href="{{ route('teacher.attendance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Attendance</a>
                            <a href="{{ route('grades.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Grades</a>
                        @endif
                        @if(auth()->user()->hasRole('student'))
                            <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">My Dashboard</a>
                            <a href="{{ route('student.attendance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Attendance</a>
                            <a href="{{ route('student.grades.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">Grades</a>
                        @endif
                        @if(auth()->user()->hasRole('parent'))
                            <a href="{{ route('parent.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50">My Dashboard</a>
                        @endif
                    @endif
                </nav>
            </div>
        </aside>

        <main class="flex-1 pl-64 lg:pl-0">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>