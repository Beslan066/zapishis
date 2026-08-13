<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'ZapisKavkaz'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{asset('favicon/favicon-96x96.png')}}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{asset('favicon.png')}}" />
    <link rel="shortcut icon" href="{{asset('favicon/favicon.ico')}}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('favicon/apple-touch-icon.png')}}" />
    <meta name="apple-mobile-web-app-title" content="Запишись" />
    <link rel="manifest" href="{{asset('favicon/site.webmanifest')}}" />

    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-gray-50">
<!-- Фон -->
<div class="fixed inset-0 -z-10 overflow-hidden">
    <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-tr from-blue-500/20 to-indigo-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-r from-indigo-500/10 to-purple-500/10 rounded-full blur-3xl"></div>
</div>

<div class="min-h-screen relative">
    <!-- НАВИГАЦИЯ - ПЕРВАЯ -->
    @include('layouts.navigation')

    <!-- КОНТЕНТ - ВТОРОЙ -->
    <main class="max-w-[80%] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50/80 backdrop-blur-sm border border-emerald-200 rounded-2xl text-emerald-700 flex items-center gap-3 shadow-lg shadow-emerald-100/50">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50/80 backdrop-blur-sm border border-rose-200 rounded-2xl text-rose-700 flex items-center gap-3 shadow-lg shadow-rose-100/50">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
@vite(['resources/js/app.js'])
</body>
</html>
