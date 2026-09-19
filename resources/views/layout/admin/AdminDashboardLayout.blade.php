<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NgekostYuk') | Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-page text-cream-900 font-sans antialiased">
    <div class="flex min-h-screen">
        @include('partial.sidebar')

        <div class="flex-1 flex flex-col min-w-0">
            <header class="flex items-center justify-between px-8 py-6">
                <div>
                    <h1 class="text-2xl font-bold text-cream-900">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')
                        <p class="mt-1 text-sm text-cream-600">@yield('page-subtitle')</p>
                    @endif
                </div>
                <a href="{{ route('admin.notifikasi.index') }}" class="relative text-cream-600 hover:text-sage-700 transition-colors duration-200 ease-out">
                    @include('partial.icons.bell', ['class' => 'w-6 h-6'])
                    <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-terracotta-500 rounded-full border-2 border-white"></span>
                </a>
            </header>

            <main class="flex-1 px-8 pb-8 @yield('main-bg')">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>