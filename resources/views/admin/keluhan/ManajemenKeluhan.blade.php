@extends('layout.admin.AdminDashboardLayout')

@section('page-title', 'Keluhan Penghuni')
@section('page-subtitle', 'Tinjau dan tindak lanjuti keluhan dari penghuni.')

@section('content')
    @php
        $countBaru = $keluhans->where('status', 'pending')->count();
        $countProses = $keluhans->where('status', 'process')->count();
        $countSelesai = $keluhans->where('status', 'resolved')->count();
    @endphp

    <div class="flex gap-2 animate-in">
        <button data-filter="semua" onclick="filterKeluhan('semua')"
            class="filter-tab bg-sage-700 text-white text-xs font-medium px-3.5 py-1.5 rounded-full transition-all duration-150 ease-out">
            Semua ({{ $keluhans->count() }})
        </button>
        <button data-filter="pending" onclick="filterKeluhan('pending')"
            class="filter-tab bg-cream-200 text-cream-600 text-xs font-medium px-3.5 py-1.5 rounded-full hover:bg-cream-200 transition-all duration-150 ease-out">
            Baru ({{ $countBaru }})
        </button>
        <button data-filter="process" onclick="filterKeluhan('process')"
            class="filter-tab bg-cream-200 text-cream-600 text-xs font-medium px-3.5 py-1.5 rounded-full hover:bg-cream-200 transition-all duration-150 ease-out">
            Diproses ({{ $countProses }})
        </button>
        <button data-filter="resolved" onclick="filterKeluhan('resolved')"
            class="filter-tab bg-cream-200 text-cream-600 text-xs font-medium px-3.5 py-1.5 rounded-full hover:bg-cream-200 transition-all duration-150 ease-out">
            Selesai ({{ $countSelesai }})
        </button>
    </div>

    <div id="keluhan-grid" class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 animate-in"
        style="animation-delay: 0.1s">
        @forelse ($keluhans as $k)
            @php
                $nama = $k->user->name ?? '-';
                $kamar = $k->user->penghunian->first()?->kamar?->nomor_kamar ?? '-';
                [$badgeClass, $badgeLabel] = match ($k->status) {
                    'pending' => ['bg-terracotta-100 text-terracotta-600', 'Baru'],
                    'process' => ['bg-amber-100 text-amber-600', 'Diproses'],
                    default => ['bg-sage-100 text-sage-700', 'Selesai'],
                };
            @endphp
            <div class="keluhan-card bg-white rounded-xl p-4 shadow-sm border border-cream-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-out"
                data-status="{{ $k->status }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div
                            class="w-8 h-8 rounded-full bg-sage-200 text-sage-800 text-xs font-semibold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($nama, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-cream-900 truncate">{{ $nama }}</p>
                            <p class="text-[11px] text-cream-600">Kamar {{ $kamar }}</p>
                        </div>
                    </div>
                    <span
                        class="shrink-0 {{ $badgeClass }} text-[10px] font-semibold px-2.5 py-1 rounded-full">{{ $badgeLabel }}</span>
                </div>

                <p class="mt-3 text-sm font-semibold text-cream-900">{{ $k->judul }}</p>
                <p class="mt-1 text-xs text-cream-600 line-clamp-2">{{ $k->deskripsi }}</p>

                <div class="mt-3 pt-3 border-t border-cream-100 flex items-center justify-between">
                    <p class="text-[11px] text-cream-500">{{ $k->created_at->translatedFormat('d M Y') }}</p>
                    <div class="flex items-center gap-2">
                        <button onclick="openDetailKeluhan({{ $k->id }})"
                            class="text-cream-500 hover:text-cream-800 transition-colors duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                        @if ($k->status !== 'resolved')
                            @php
                                $actionClass = match ($k->status) {
                                    'pending' => 'bg-terracotta-500 hover:bg-terracotta-600',
                                    'process' => 'bg-sage-700 hover:bg-sage-800',
                                };
                            @endphp

                            <button onclick="advanceStatus({{ $k->id }})"
                                class="text-xs font-medium text-white px-3 py-1.5 rounded-lg transition-colors duration-150 {{ $actionClass }}">
                                {{ $k->status === 'pending' ? 'Proses' : 'Selesai' }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-cream-200 px-4 py-8 text-center shadow-sm">
                <p class="text-sm font-medium text-cream-800">
                    Belum ada keluhan.
                </p>
                <p class="mt-1 text-xs text-cream-500">
                    Keluhan penghuni yang masuk akan tampil di sini.
                </p>
            </div>
        @endforelse
    </div>

    <div id="keluhan-empty-filter"
        class="hidden col-span-full bg-white rounded-xl border border-cream-200 px-4 py-8 text-center shadow-sm">
        <p id="keluhan-empty-title" class="text-sm font-medium text-cream-800"></p>
        <p id="keluhan-empty-desc" class="mt-1 text-xs text-cream-500"></p>
    </div>

    <div id="keluhan-pagination" class="hidden flex items-center justify-between pt-4">
        <p id="keluhan-page-info" class="text-xs text-cream-600"></p>
        <div class="flex gap-2">
            <button onclick="changeKeluhanPage(-1)" id="keluhan-btn-prev"
                class="text-xs font-medium text-cream-700 border border-cream-300 rounded-full px-3.5 py-1.5 hover:bg-cream-100 transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                Sebelumnya
            </button>
            <button onclick="changeKeluhanPage(1)" id="keluhan-btn-next"
                class="text-xs font-medium text-cream-700 border border-cream-300 rounded-full px-3.5 py-1.5 hover:bg-cream-100 transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                Berikutnya
            </button>
        </div>
    </div>

    <div id="modal-detail-keluhan" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeDetailKeluhan()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-md animate-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200">
                <h3 class="text-base font-semibold text-cream-900">Detail Keluhan</h3>
                <button onclick="closeDetailKeluhan()"
                    class="text-cream-400 hover:text-cream-700 transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5">
                <div class="flex items-center gap-3">
                    <div id="dk-avatar"
                        class="w-10 h-10 rounded-full bg-sage-200 text-sage-800 text-sm font-semibold flex items-center justify-center shrink-0">
                    </div>
                    <div>
                        <p id="dk-nama" class="text-sm font-semibold text-cream-900"></p>
                        <p id="dk-kamar" class="text-[11px] text-cream-600"></p>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <p id="dk-judul" class="text-sm font-semibold text-cream-900"></p>
                    <span id="dk-badge" class="text-[10px] font-semibold px-2.5 py-1 rounded-full"></span>
                </div>
                <p id="dk-deskripsi" class="mt-2 text-sm text-cream-600 bg-cream-50 rounded-lg p-3 leading-relaxed"></p>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
                <button onclick="closeDetailKeluhan()"
                    class="text-sm font-medium text-cream-600 hover:text-cream-900 transition-colors duration-150">Tutup</button>
                <button id="dk-action-btn"
                    class="text-white text-sm font-medium px-5 py-2.5 rounded-full transition-all duration-150 ease-out">
                    Tandai Selesai
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const keluhanData = @json($keluhans->keyBy('id'));
        let currentKeluhanId = null;
        let activeFilter = 'semua';
        let keluhanPage = 1;
        const KELUHAN_PAGE_SIZE = 6;

        function filterKeluhan(status) {
            activeFilter = status;
            keluhanPage = 1;

            document.querySelectorAll('.filter-tab').forEach(t => {
                const active = t.dataset.filter === status;
                t.classList.toggle('bg-sage-700', active);
                t.classList.toggle('text-white', active);
                t.classList.toggle('bg-cream-100', !active);
                t.classList.toggle('text-cream-600', !active);
            });

            renderKeluhanPage();
        }

        function renderKeluhanPage() {
            const allCards = Array.from(document.querySelectorAll('.keluhan-card'));
            const visible = allCards.filter(c => activeFilter === 'semua' || c.dataset.status === activeFilter);

            const emptyState = document.getElementById('keluhan-empty-filter');
            const emptyTitle = document.getElementById('keluhan-empty-title');
            const emptyDesc = document.getElementById('keluhan-empty-desc');

            const filterMessages = {
                pending: {
                    title: 'Belum ada keluhan baru.',
                    desc: 'Semua keluhan yang masuk sudah ditangani atau belum ada keluhan baru dari penghuni.'
                },
                process: {
                    title: 'Tidak ada keluhan yang sedang diproses.',
                    desc: 'Keluhan yang sedang ditindaklanjuti akan muncul di sini.'
                },
                resolved: {
                    title: 'Belum ada keluhan yang selesai.',
                    desc: 'Keluhan yang sudah terselesaikan akan muncul di sini.'
                }
            };

            if (visible.length === 0 && activeFilter !== 'semua') {
                emptyState.classList.remove('hidden');

                emptyTitle.textContent = filterMessages[activeFilter].title;
                emptyDesc.textContent = filterMessages[activeFilter].desc;
            } else {
                emptyState.classList.add('hidden');
            }

            allCards.forEach(c => c.style.display = 'none');

            const totalPages = Math.max(1, Math.ceil(visible.length / KELUHAN_PAGE_SIZE));
            if (keluhanPage > totalPages) keluhanPage = totalPages;

            visible.forEach((card, i) => {
                const page = Math.floor(i / KELUHAN_PAGE_SIZE) + 1;
                if (page === keluhanPage) card.style.display = '';
            });

            const pag = document.getElementById('keluhan-pagination');
            if (visible.length > KELUHAN_PAGE_SIZE) {
                pag.classList.remove('hidden');
                document.getElementById('keluhan-page-info').textContent = `Halaman ${keluhanPage} dari ${totalPages}`;
                document.getElementById('keluhan-btn-prev').disabled = keluhanPage <= 1;
                document.getElementById('keluhan-btn-next').disabled = keluhanPage >= totalPages;
            } else {
                pag.classList.add('hidden');
            }
        }

        function changeKeluhanPage(delta) {
            keluhanPage += delta;
            renderKeluhanPage();
        }

        document.addEventListener('DOMContentLoaded', renderKeluhanPage);

        const statusMeta = {
            pending: {
                badge: 'bg-terracotta-100 text-terracotta-600',
                label: 'Baru',
                action: 'Proses'
            },
            process: {
                badge: 'bg-amber-100 text-amber-600',
                label: 'Diproses',
                action: 'Tandai Selesai'
            },
            resolved: {
                badge: 'bg-sage-100 text-sage-700',
                label: 'Selesai',
                action: null
            },
        };

        function openDetailKeluhan(id) {
            currentKeluhanId = id;
            const k = keluhanData[id];
            const nama = k.user?.name ?? '-';
            const kamar = k.user?.penghunian?.[0]?.kamar?.nomor_kamar ?? '-';
            const meta = statusMeta[k.status];

            document.getElementById('dk-avatar').textContent = nama.charAt(0).toUpperCase();
            document.getElementById('dk-nama').textContent = nama;
            document.getElementById('dk-kamar').textContent = `Kamar ${kamar}`;
            document.getElementById('dk-judul').textContent = k.judul;
            document.getElementById('dk-deskripsi').textContent = k.deskripsi;

            const badgeEl = document.getElementById('dk-badge');
            badgeEl.className = 'text-[10px] font-semibold px-2.5 py-1 rounded-full ' + meta.badge;
            badgeEl.textContent = meta.label;

            const actionBtn = document.getElementById('dk-action-btn');
            if (meta.action) {
                actionBtn.classList.remove('hidden');
                actionBtn.textContent = meta.action;

                actionBtn.classList.remove(
                    'bg-sage-700',
                    'hover:bg-sage-800',
                    'bg-terracotta-500',
                    'hover:bg-terracotta-600'
                );

                if (k.status === 'pending') {
                    actionBtn.classList.add(
                        'bg-terracotta-500',
                        'hover:bg-terracotta-600'
                    );
                } else if (k.status === 'process') {
                    actionBtn.classList.add(
                        'bg-sage-700',
                        'hover:bg-sage-800'
                    );
                }
            } else {
                actionBtn.classList.add('hidden');
            }

            document.getElementById('modal-detail-keluhan').classList.remove('hidden');
        }

        function closeDetailKeluhan() {
            document.getElementById('modal-detail-keluhan').classList.add('hidden');
        }

        async function advanceStatus(id) {
            try {
                const res = await fetch(`/admin/keluhan/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                if (!res.ok) throw new Error();
                window.location.reload();
            } catch (err) {
                alert('Gagal memperbarui status keluhan.');
            }
        }

        function advanceStatusFromDetail() {
            advanceStatus(currentKeluhanId);
        }
    </script>
@endpush
