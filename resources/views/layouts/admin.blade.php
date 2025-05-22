<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Casitas') }} - Panel de Administración</title>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts y estilos -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="flex-shrink-0 flex items-center">
                            <span class="text-xl font-bold text-blue-600">Casitas</span>
                            <span class="ml-2 text-gray-600 dark:text-gray-400">|</span>
                            <span class="ml-2 text-gray-600 dark:text-gray-400">Administración</span>
                        </a>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-4">
                            <a href="{{ route('admin.dashboard') }}"
                                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : 'nav-link-dark' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.properties.index') }}"
                                class="nav-link {{ request()->routeIs('admin.properties.*') ? 'nav-link-active' : 'nav-link-dark' }}">
                                Propiedades
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                                class="nav-link {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : 'nav-link-dark' }}">
                                Usuarios
                            </a>
                            <a href="{{ route('admin.features.index') }}"
                                class="nav-link {{ request()->routeIs('admin.features.*') ? 'nav-link-active' : 'nav-link-dark' }}">
                                Características
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="nav-link-dark mr-2" title="Ver sitio web">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        <div class="ml-3 relative">
                            <div class="flex items-center">
                                <button type="button"
                                    class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    id="user-menu-button">
                                    <span class="sr-only">Abrir menú de usuario</span>
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-600 text-white flex items-center justify-center">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                </button>
                                <span class="ml-2 text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    @yield('header', 'Panel de Administración')
                </h1>
            </div>
        </header>

        <main class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="alert alert-success mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} Casitas - Panel de Administración
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
