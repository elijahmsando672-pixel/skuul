<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SKUUL Management System') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col justify-center sm:justify-center px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="w-12 h-12 bg-skuul-600 rounded-lg flex items-center justify-center text-white font-bold text-lg mx-auto mb-4">
                    S
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 tracking-tight">
                    SKUUL Management System
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    School Management Platform
                </p>
            </div>
            <div class="mt-8">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>