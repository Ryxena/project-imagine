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
        <div id="sidebar-overlay" onclick="toggleMobileSidebar()"
            class="hidden fixed inset-0 bg-cream-900/40 z-40 lg:hidden">
        </div>

        @include('partial.sidebar')


        <div class="flex-1 flex flex-col min-w-0">
            <header
                class="lg:hidden fixed top-0 inset-x-0 z-30 h-16 bg-cream-page/90 backdrop-blur border-b border-cream-200">

                <div class="h-full px-4 flex items-center justify-between">

                    <button onclick="toggleMobileSidebar()"
                        class="w-9 h-9 rounded-xl bg-white border border-cream-200 text-cream-700 flex items-center justify-center hover:bg-cream-50 transition-colors">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />

                        </svg>

                    </button>


                    <p class="text-sm font-bold text-cream-900">
                        NgekostYuk
                    </p>


                    <a href="{{ route('admin.notifikasi.index') }}"
                        class="relative text-cream-600 hover:text-sage-700 transition-colors">
                        @include('partial.icons.bell', ['class' => 'w-5 h-5'])
                        <span
                            class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-terracotta-500 rounded-full border border-white">
                        </span>
                    </a>
                </div>
            </header>
            <header class="hidden lg:flex items-center justify-between px-8 py-6">
                <div>
                    <h1 class="text-2xl font-bold text-cream-900">
                        @yield('page-title', 'Dashboard')
                    </h1>

                    @hasSection('page-subtitle')
                        <p class="mt-1 text-sm text-cream-600">
                            @yield('page-subtitle')
                        </p>
                    @endif
                </div>
                <a href="{{ route('admin.notifikasi.index') }}"
                    class="relative text-cream-600 hover:text-sage-700 transition-colors duration-200 ease-out">

                    @include('partial.icons.bell', ['class' => 'w-6 h-6'])

                    <span
                        class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-terracotta-500 rounded-full border-2 border-white">
                    </span>
                </a>
            </header>
            <main class="flex-1 px-4 pt-20 pb-8 lg:px-8 lg:pt-0 @yield('main-bg')">
                @yield('content')
            </main>
        </div>
    </div>


    @stack('scripts')
    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            const isClosed = sidebar.classList.contains('-translate-x-full');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');

            document.body.style.overflow = isClosed ? 'hidden' : '';
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');

            document.body.style.overflow = '';
        }
    </script>
</body>

</html>
