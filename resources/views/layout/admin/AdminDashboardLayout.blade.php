<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title', 'NgekostYuk') | Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-cream-page text-cream-900 font-sans antialiased relative">
    <div id="toast-container"
        class="fixed top-4 right-4 z-[100] flex flex-col gap-3 pointer-events-none w-[340px] max-w-[calc(100vw-2rem)]">
    </div>

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

                    <p class="text-sm font-bold text-cream-900">NgekostYuk</p>

                    <div class="relative">
                        <button onclick="toggleNotifPanel('mobile')" id="notif-btn-mobile"
                            class="relative text-cream-600 hover:text-sage-700 transition-colors">
                            @include('partial.icons.bell', ['class' => 'w-5 h-5'])
                            <span id="notif-dot-mobile"
                                class="hidden absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center bg-terracotta-500 text-white text-[9px] font-bold rounded-full border-2 border-white"></span>
                        </button>

                        <div id="notif-panel-mobile"
                            class="hidden fixed left-4 right-4 top-16 z-50 bg-white rounded-2xl shadow-xl border border-cream-200 max-h-[70vh] overflow-hidden flex flex-col transition-all duration-200 ease-out transform scale-95 opacity-0 data-[open=true]:scale-100 data-[open=true]:opacity-100">
                        </div>
                    </div>
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

                <div class="relative">
                    <button onclick="toggleNotifPanel('desktop')" id="notif-btn-desktop"
                        class="relative text-cream-600 hover:text-sage-700 transition-colors duration-200 ease-out">
                        @include('partial.icons.bell', ['class' => 'w-6 h-6'])
                        <span id="notif-dot-desktop"
                            class="hidden absolute -top-1.5 -right-2 min-w-[20px] h-[20px] px-1.5 flex items-center justify-center bg-terracotta-500 text-white text-[10px] font-bold rounded-full border-2 border-white shadow-sm"></span>
                    </button>

                    <div id="notif-panel-desktop"
                        class="hidden absolute right-0 top-full mt-2.5 w-96 z-50 bg-white rounded-2xl shadow-xl border border-cream-200 max-h-[70vh] overflow-hidden flex flex-col transition-all duration-200 ease-out transform scale-95 opacity-0 data-[open=true]:scale-100 data-[open=true]:opacity-100">
                    </div>
                </div>
            </header>
            <main class="flex-1 px-4 pt-20 pb-8 lg:px-8 lg:pt-0 @yield('main-bg')">
                <div class="block lg:hidden mb-6 pt-2">
                    <h1 class="text-xl font-bold text-cream-900">
                        @yield('page-title', 'Dashboard')
                    </h1>
                    @hasSection('page-subtitle')
                        <p class="mt-1 text-xs text-cream-600 leading-relaxed">
                            @yield('page-subtitle')
                        </p>
                    @endif
                </div>

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

        const svgReceipt =
            `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>`;
        const svgShield =
            `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>`;
        const svgChat =
            `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>`;
        const svgMegaphone =
            `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 01-4.5-4.5V9.75A4.5 4.5 0 017.5 5.25h.75c.704 0 1.402-.03 2.09-.09m0 10.68l3.47 2.31a.75.75 0 001.19-.62V3.62a.75.75 0 00-1.19-.62l-3.47 2.31m0 10.68v-10.68" /></svg>`;
        const svgBell =
            `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>`;

        const notifTypeMeta = {
            tagihan: {
                rawSvg: svgReceipt,
                tint: 'bg-sage-100 text-sage-700',
                route: "{{ route('admin.tagihan.index') }}"
            },
            pembayaran: {
                rawSvg: svgShield,
                tint: 'bg-amber-100 text-amber-600',
                route: "{{ route('admin.verifikasipembayaran.index') }}"
            },
            keluhan: {
                rawSvg: svgChat,
                tint: 'bg-terracotta-100 text-terracotta-600',
                route: "{{ route('admin.keluhan.index') }}"
            },
            pengumuman: {
                rawSvg: svgMegaphone,
                tint: 'bg-sage-100 text-sage-700',
                route: "{{ route('admin.pengumuman.index') }}"
            }
        };
        const notifFallback = {
            rawSvg: svgBell,
            tint: 'bg-cream-100 text-cream-600',
            route: null
        };

        let lastNotifId = null;

        async function fetchNotifCount() {
            try {
                const res = await fetch("{{ route('admin.notifikasi.index') }}", {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const json = await res.json();

                const count = json.unread_count || 0;
                const displayCount = count > 99 ? '99+' : count;

                ['desktop', 'mobile'].forEach(ctx => {
                    const dot = document.getElementById(`notif-dot-${ctx}`);
                    if (dot) {
                        dot.innerText = displayCount;
                        dot.classList.toggle('hidden', count === 0);
                    }
                });

                if (json.data && json.data.length > 0) {
                    const latestNotif = json.data[0];

                    if (lastNotifId !== null && latestNotif.id > lastNotifId && !latestNotif.dibaca) {
                        showToastNotif(latestNotif);
                    }

                    lastNotifId = latestNotif.id > (lastNotifId || 0) ? latestNotif.id : lastNotifId;
                }

            } catch (err) {
                console.error('Gagal mengambil data notifikasi:', err);
            }
        }

        function showToastNotif(n) {
            const container = document.getElementById('toast-container');
            const meta = notifTypeMeta[n.tipe] || notifFallback;

            const toast = document.createElement('div');
            toast.className =
                'pointer-events-auto bg-white rounded-2xl shadow-xl shadow-cream-900/5 border border-cream-200 p-4 flex items-start gap-3.5 transform transition-all duration-500 ease-out translate-x-[120%] opacity-0 cursor-pointer hover:bg-cream-50';

            toast.innerHTML = `
                <div class="w-10 h-10 rounded-xl ${meta.tint} flex items-center justify-center shrink-0">
                    ${meta.rawSvg}
                </div>
                <div class="flex-1 min-w-0" onclick="handleNotifClick(${n.id}, ${meta.route ? `'${meta.route}'` : 'null'})">
                    <div class="flex justify-between items-start">
                        <p class="text-sm font-bold text-cream-900 truncate pr-2">${n.judul}</p>
                        <p class="text-[10px] font-medium text-sage-600 whitespace-nowrap shrink-0 pt-0.5">Baru saja</p>
                    </div>
                    <p class="text-[11px] text-cream-600 line-clamp-2 mt-0.5 leading-relaxed">${n.pesan}</p>
                </div>
                <button class="text-cream-400 hover:text-cream-700 transition-colors p-1" onclick="this.parentElement.remove(); event.stopPropagation();">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;

            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-[120%]', 'opacity-0');
            });

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.classList.add('opacity-0', 'translate-x-[120%]');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 6000);
        }

        function timeAgo(dateStr) {
            const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
            return Math.floor(diff / 86400) + ' hari lalu';
        }

        async function toggleNotifPanel(ctx) {
            const panel = document.getElementById(`notif-panel-${ctx}`);
            const otherCtx = ctx === 'desktop' ? 'mobile' : 'desktop';
            document.getElementById(`notif-panel-${otherCtx}`).classList.add('hidden');
            document.getElementById(`notif-panel-${otherCtx}`).removeAttribute('data-open');

            const isHidden = panel.classList.contains('hidden');

            if (isHidden) {
                panel.classList.remove('hidden');
                setTimeout(() => panel.setAttribute('data-open', 'true'), 10);
                panel.innerHTML = `
            <div class="flex items-center justify-between px-4 py-3 border-b border-cream-200 shrink-0">
                <p class="text-sm font-semibold text-cream-900">Notifikasi</p>
            </div>
            <div class="p-8 text-center text-xs text-cream-500 flex flex-col items-center justify-center gap-2">
                <div class="w-5 h-5 border-2 border-sage-600 border-t-transparent rounded-full animate-spin"></div>
                <span>Memuat notifikasi...</span>
            </div>
        `;

                try {
                    const res = await fetch('{{ route('admin.notifikasi.index') }}', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    if (panel.getAttribute('data-open') === 'true') {
                        renderNotifPanel(panel, json.data);
                    }
                } catch (err) {
                    if (panel.getAttribute('data-open') === 'true') {
                        panel.innerHTML = `
                    <div class="flex items-center justify-between px-4 py-3 border-b border-cream-200 shrink-0">
                        <p class="text-sm font-semibold text-cream-900">Notifikasi</p>
                    </div>
                    <div class="p-8 text-center text-xs text-terracotta-600">Gagal memuat notifikasi.</div>
                `;
                    }
                }
            } else {
                panel.removeAttribute('data-open');
                setTimeout(() => panel.classList.add('hidden'), 200);
            }
        }

        function renderNotifPanel(panel, notifications) {
            const header = `
                <div class="flex items-center justify-between px-4 py-3 border-b border-cream-200 shrink-0">
                    <p class="text-sm font-semibold text-cream-900">Notifikasi</p>
                    <button onclick="markAllRead()" class="text-[11px] font-medium text-sage-700 hover:text-sage-800 transition-colors duration-150">Tandai semua dibaca</button>
                </div>
            `;

            if (notifications.length === 0) {
                panel.innerHTML = header +
                    `<div class="p-8 text-center text-xs text-cream-500">Belum ada notifikasi.</div>`;
                return;
            }

            const items = notifications.map(n => {
                const meta = notifTypeMeta[n.tipe] || notifFallback;

                return `
                    <button onclick="handleNotifClick(${n.id}, ${meta.route ? `'${meta.route}'` : 'null'})"
                        class="w-full flex items-start gap-3 px-4 py-3 hover:bg-sage-50/80 transition-colors duration-150 text-left bg-sage-50/40">
                        <span class="w-2 h-2 bg-sage-600 rounded-full shrink-0 mt-1.5"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-cream-900">${n.judul}</p>
                            <p class="text-[11px] text-cream-600 mt-0.5 leading-relaxed">${n.pesan}</p>
                            <p class="text-[10px] text-cream-400 mt-1">${timeAgo(n.created_at)}</p>
                        </div>
                    </button>
                `;
            }).join('');

            const footer = notifications.length >= 30 ? `
                <div class="px-4 py-2.5 border-t border-cream-200 bg-cream-50/50 text-center shrink-0">
                    <p class="text-[10px] text-cream-500">Menampilkan 30 notifikasi terakhir</p>
                </div>
            ` : '';

            panel.innerHTML = header + `<div class="overflow-y-auto divide-y divide-cream-100 flex-1">${items}</div>` +
                footer;
        }

        async function handleNotifClick(id, redirectUrl) {
            try {
                await fetch(`/admin/notifikasi/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
            } catch (err) {
                console.error('Gagal menandai notifikasi:', err);
            }

            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                fetchNotifCount();
                document.querySelectorAll('[id^="notif-panel-"]').forEach(p => p.classList.add('hidden'));
            }
        }

        async function markAllRead() {
            try {
                await fetch('{{ route('admin.notifikasi.markallread') }}', {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                document.querySelectorAll('[id^="notif-panel-"]:not(.hidden)').forEach(async (panel) => {
                    const res = await fetch('{{ route('admin.notifikasi.index') }}', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    renderNotifPanel(panel, json.data);
                });
                fetchNotifCount();
            } catch (err) {
                console.error('Gagal menandai semua notifikasi:', err);
            }
        }

        document.addEventListener('click', (e) => {
            ['desktop', 'mobile'].forEach(ctx => {
                const btn = document.getElementById(`notif-btn-${ctx}`);
                const panel = document.getElementById(`notif-panel-${ctx}`);
                if (btn && panel && !btn.contains(e.target) && !panel.contains(e.target)) {
                    if (!panel.classList.contains('hidden')) {
                        panel.removeAttribute('data-open');
                        setTimeout(() => panel.classList.add('hidden'), 200);
                    }
                }
            });
        });

        fetchNotifCount();

        setInterval(fetchNotifCount, 15000);
    </script>
</body>

</html>
