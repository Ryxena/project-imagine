@php
    $isAdmin = auth()->user()->role === 'super_admin';

    $menu = $isAdmin
        ? [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'grid'],
            ['route' => 'admin.kamar.index', 'label' => 'Kamar', 'icon' => 'door'],
            ['route' => 'admin.penghuni.index', 'label' => 'Penghuni', 'icon' => 'users'],
            ['route' => 'admin.tagihan.index', 'label' => 'Tagihan', 'icon' => 'receipt'],
            ['route' => 'admin.verifikasipembayaran.index', 'label' => 'Verifikasi', 'icon' => 'shield'],
            ['route' => 'admin.keluhan.index', 'label' => 'Keluhan', 'icon' => 'warning'],
            ['route' => 'admin.pengumuman.index', 'label' => 'Pengumuman', 'icon' => 'megaphone'],
        ]
        : [
            ['route' => 'penghuni.dashboard', 'label' => 'Dashboard', 'icon' => 'grid'],
            ['route' => 'penghuni.tagihan.index', 'label' => 'Tagihan', 'icon' => 'receipt'],
            ['route' => 'penghuni.keluhan.index', 'label' => 'Keluhan', 'icon' => 'warning'],
            ['route' => 'penghuni.pengumuman.index', 'label' => 'Pengumuman', 'icon' => 'megaphone'],
        ];

    $profileRoute = $isAdmin ? 'admin.profile.show' : 'penghuni.profile.show';
    $roleLabel = $isAdmin ? 'Owner' : 'Penghuni';
@endphp

<aside id="sidebar"
    class="
    fixed inset-y-0 left-0 z-50
    w-56
    -translate-x-full lg:translate-x-0
    bg-sage-700 border-r border-sage-800
    flex flex-col justify-between
    transition-[transform] duration-300 ease-in-out
    lg:static lg:shrink-0
    lg:transition-[width] lg:duration-300
    ">
    <div>
        <div class="px-5 py-6 flex items-center">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 relative cursor-pointer hover:opacity-80 transition-opacity duration-200"
                onclick="handleSidebarLogoClick()">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Icon" class="w-full h-full object-contain">
            </div>
            <div
                class="sidebar-text ml-2 overflow-hidden whitespace-nowrap transition-all duration-300 opacity-100 flex flex-col justify-center">
                <h1 class="text-xl font-bold leading-none mb-1.5">
                    <span class="text-white">Ngekost</span><span class="text-terracotta-400">Yuk</span>
                </h1>
                <p class="text-[11px] text-sage-200 leading-none">Kelola Kost, Tanpa Ribet!</p>
            </div>
        </div>

        <nav class="space-y-1">
            @foreach ($menu as $item)
                @php
                    $active = request()->routeIs($item['route'] . '*');
                    $badgeCount = 0;
                    $user = auth()->user();

                    if ($isAdmin) {
                        if ($item['route'] === 'admin.verifikasipembayaran.index') {
                            $lastRead = $user->last_read_verifikasi ?? now()->subYears(5);

                            $badgeCount = \App\Models\Pembayaran::where('status_verifikasi', 'pending')
                                ->where('created_at', '>', $lastRead)
                                ->count();
                        } elseif ($item['route'] === 'admin.keluhan.index') {
                            $lastRead = $user->last_read_keluhan_admin ?? now()->subYears(5);

                            $badgeCount = \App\Models\Keluhan::where('created_at', '>', $lastRead)->count();
                        }
                    } else {
                        if ($item['route'] === 'penghuni.tagihan.index') {
                            $lastRead = $user->last_read_tagihan_penghuni ?? now()->subYears(5);

                            $badgeCount = \App\Models\Tagihan::whereHas(
                                'penghunian',
                                fn($q) => $q->where('user_id', $user->id),
                            )
                                ->where('status_pembayaran', '!=', 'lunas')
                                ->where('created_at', '>', $lastRead)
                                ->count();
                        } elseif ($item['route'] === 'penghuni.keluhan.index') {
                            $lastRead = $user->last_read_keluhan_penghuni ?? now()->subYears(5);

                            $badgeCount = \App\Models\Keluhan::where('user_id', $user->id)
                                ->where('status', '!=', 'resolved')
                                ->where('updated_at', '>', $lastRead)
                                ->count();
                        } elseif ($item['route'] === 'penghuni.pengumuman.index') {
                            $lastRead = $user->last_read_pengumuman_penghuni ?? now()->subYears(5);

                            $badgeCount = \App\Models\Pengumuman::where('created_at', '>', $lastRead)->count();
                        }
                    }
                @endphp

                <div class="relative px-3">
                    <a href="{{ route($item['route']) }}" onclick="closeMobileSidebar()"
                        class="group relative z-10 flex items-center justify-between px-4 py-2 rounded-xl text-[13px] font-medium transition-all duration-200 ease-out
                        {{ $active ? 'bg-cream-page text-sage-900' : 'text-sage-100 hover:bg-sage-600 hover:text-white' }}">

                        <div class="flex items-center min-w-0">
                            <span
                                class="w-5 flex justify-center shrink-0 transition-colors duration-200 {{ $active ? 'text-sage-700' : 'text-sage-300 group-hover:text-white' }}">
                                @include('partial.icons.' . $item['icon'], ['class' => 'w-8 h-8'])
                            </span>

                            <span
                                class="sidebar-text ml-2.5 overflow-hidden whitespace-nowrap transition-all duration-300 opacity-100 truncate">
                                {{ $item['label'] }}
                            </span>
                        </div>

                        @if ($badgeCount > 0)
                            <span
                                class="sidebar-text shrink-0 bg-terracotta-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                                {{ $badgeCount }}
                            </span>
                        @endif
                    </a>
                </div>
            @endforeach
        </nav>
    </div>

    <div class="relative">
        <button onclick="toggleUserMenu()" id="user-menu-btn"
            class="w-full px-5 py-4 border-t border-sage-600 flex items-center transition-colors duration-150 hover:bg-sage-600">

            @if (auth()->user()->image)
                <img src="{{ asset('storage/' . auth()->user()->image) }}" alt="Foto {{ auth()->user()->name }}"
                    class="w-8 h-8 rounded-full object-cover shrink-0 border border-sage-400 shadow-sm">
            @else
                <div
                    class="w-8 h-8 rounded-full bg-sage-500 flex items-center justify-center text-white font-semibold text-xs shrink-0 border border-sage-400">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            @endif

            <div
                class="sidebar-text ml-3 overflow-hidden whitespace-nowrap transition-all duration-300 opacity-100 min-w-0 flex-1 text-left w-full">
                <p class="text-[13px] font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-sage-300">{{ $roleLabel }}</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" id="user-menu-chevron"
                class="sidebar-text transition-all duration-300 opacity-100 w-4 h-4 text-sage-300 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
            </svg>
        </button>

        <div id="user-menu-dropdown"
            class="hidden absolute bottom-full left-3 w-48 mb-1 bg-white rounded-xl shadow-lg border border-cream-200 overflow-hidden">
            <a href="{{ route($profileRoute) }}"
                class="flex items-center gap-2.5 px-4 py-3 text-sm text-cream-900 hover:bg-cream-100 transition-colors duration-150">
                @include('partial.icons.users', ['class' => 'w-4 h-4 text-cream-500'])
                Profil Saya
            </a>
            <button onclick="openLogoutModal()"
                class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-terracotta-600 hover:bg-terracotta-50 transition-colors duration-150 text-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
                Keluar
            </button>
        </div>
    </div>

</aside>
<div id="modal-logout" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-cream-900/40" onclick="closeLogoutModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-sm animate-in">
        <div class="px-6 py-5">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-terracotta-100 text-terracotta-600 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-cream-900">Keluar dari akun?</h3>
            </div>
            <p class="mt-3 text-sm text-cream-600">Kamu perlu login kembali untuk mengakses dashboard.</p>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
            <button onclick="closeLogoutModal()"
                class="text-sm font-medium text-cream-600 hover:text-cream-900 transition-colors duration-150">Batal</button>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-terracotta-600 text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-terracotta-700 active:scale-[0.98] transition-all duration-150 ease-out">
                    Ya, Keluar
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    if (localStorage.getItem('sidebar_collapsed') === 'true') {
        const sidebar = document.getElementById('sidebar');
        const texts = document.querySelectorAll('.sidebar-text');

        sidebar.classList.remove('transition-[width]', 'duration-300');
        texts.forEach(el => el.classList.remove('transition-all', 'duration-300'));

        sidebar.classList.remove('w-56');
        sidebar.classList.add('w-[72px]');

        texts.forEach(el => {
            el.classList.remove('w-full', 'opacity-100', 'ml-3', 'ml-2.5');
            el.classList.add('w-0', 'opacity-0');
            if (el.id === 'user-menu-chevron') {
                el.classList.add('hidden');
            }
        });

        setTimeout(() => {
            sidebar.classList.add('transition-[width]', 'duration-300');
            texts.forEach(el => el.classList.add('transition-all', 'duration-300'));
        }, 10);
    }
</script>

@push('scripts')
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const texts = document.querySelectorAll('.sidebar-text');

            const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';

            if (!isCollapsed) {
                sidebar.classList.remove('w-56');
                sidebar.classList.add('w-[72px]');

                texts.forEach(el => {
                    el.classList.remove('w-full', 'opacity-100', 'ml-3', 'ml-2.5');
                    el.classList.add('w-0', 'opacity-0');

                    if (el.id === 'user-menu-chevron') {
                        el.classList.add('hidden');
                    }
                });

                localStorage.setItem('sidebar_collapsed', 'true');

            } else {
                sidebar.classList.remove('w-[72px]');
                sidebar.classList.add('w-56');

                texts.forEach(el => {
                    el.classList.remove('w-0', 'opacity-0');

                    if (el.tagName.toLowerCase() === 'span') {
                        el.classList.add('w-full', 'opacity-100', 'ml-2.5');
                    } else if (el.id !== 'user-menu-chevron') {
                        el.classList.add('w-full', 'opacity-100', 'ml-3');
                    }
                });

                localStorage.setItem('sidebar_collapsed', 'false');
            }
        }

        function handleSidebarLogoClick() {
            if (window.innerWidth >= 1024) {
                toggleSidebar();
            } else {
                toggleMobileSidebar();
            }
        }

        function toggleUserMenu() {
            document.getElementById('user-menu-dropdown').classList.toggle('hidden');

            const chevron = document.getElementById('user-menu-chevron');
            if (!chevron.classList.contains('hidden')) {
                chevron.classList.toggle('rotate-180');
            }
        }

        document.addEventListener('click', (e) => {
            const btn = document.getElementById('user-menu-btn');
            const dropdown = document.getElementById('user-menu-dropdown');
            if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
                document.getElementById('user-menu-chevron').classList.remove('rotate-180');
            }
        });

        function openLogoutModal() {
            document.getElementById('user-menu-dropdown').classList.add('hidden');
            document.getElementById('user-menu-chevron').classList.remove('rotate-180');
            document.getElementById('modal-logout').classList.remove('hidden');
        }

        function closeLogoutModal() {
            document.getElementById('modal-logout').classList.add('hidden');
        }
    </script>
@endpush
