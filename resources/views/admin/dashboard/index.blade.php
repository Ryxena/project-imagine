@extends('layout.admin.AdminDashboardLayout')

@php
    $hour = now('Asia/Jakarta')->hour;
    $greeting = match (true) {
        $hour < 11 => 'Selamat Pagi',
        $hour < 15 => 'Selamat Siang',
        $hour < 18 => 'Selamat Sore',
        default => 'Selamat Malam',
    };
@endphp

@section('page-title')
    <span class="flex items-center gap-3">
        <span>{{ $greeting }}, {{ auth()->user()->name }}!</span>
    </span>
@endsection
@section('page-subtitle', 'Ringkasan operasional dan hal-hal yang perlu atensimu hari ini.')

@section('content')
    <div class="flex items-center gap-2.5 overflow-x-auto pb-2 sm:pb-0 whitespace-nowrap animate-in mb-6 no-scrollbar"
        style="-ms-overflow-style: none; scrollbar-width: none;">
        <style>
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }
        </style>

        <a href="{{ route('admin.penghuni.index') }}?action=create"
            class="shrink-0 inline-flex items-center gap-2 bg-sage-700 text-white text-xs font-semibold px-4.5 py-2.5 rounded-full hover:bg-sage-800 hover:shadow-md active:scale-[0.98] transition-all duration-150 ease-out shadow-sm">
            @include('partial.icons.users', ['class' => 'w-4 h-4'])
            Tambah Penghuni
        </a>

        <button onclick="generateTagihanFromDashboard()" id="btn-generate-dash"
            class="shrink-0 inline-flex items-center gap-2 bg-white border border-cream-300 text-cream-800 text-xs font-semibold px-4.5 py-2.5 rounded-full hover:bg-cream-50 hover:border-cream-400 hover:text-cream-900 hover:shadow-sm active:scale-[0.98] transition-all duration-150 ease-out shadow-2xs">
            @include('partial.icons.receipt', ['class' => 'w-4 h-4'])
            Generate Tagihan
        </button>

        <a href="{{ route('admin.pengumuman.index') }}?action=create"
            class="shrink-0 inline-flex items-center gap-2 bg-white border border-cream-300 text-cream-800 text-xs font-semibold px-4.5 py-2.5 rounded-full hover:bg-cream-50 hover:border-cream-400 hover:text-cream-900 hover:shadow-sm active:scale-[0.98] transition-all duration-150 ease-out shadow-2xs">
            @include('partial.icons.megaphone', ['class' => 'w-4 h-4'])
            Buat Pengumuman
        </a>
    </div>

    <p id="generate-dash-status" class="hidden -mt-3 mb-4 text-xs font-medium text-sage-700 animate-in"></p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-5">
        <a href="{{ route('admin.kamar.index') }}"
            class="group animate-in block bg-white rounded-2xl p-5 shadow-sm border border-cream-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-out"
            style="animation-delay: 0.05s">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-cream-500">Kamar Terisi</p>
                <div
                    class="w-10 h-10 rounded-xl bg-sage-100 text-sage-700 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    @include('partial.icons.door', ['class' => 'w-5 h-5'])
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-cream-900 tracking-tight">
                {{ $kpi['kamar_terisi'] }} <span class="text-base font-medium text-cream-400">/
                    {{ $kpi['kamar_total'] }}</span>
            </p>
            <div class="mt-4 h-1.5 bg-cream-100 rounded-full overflow-hidden">
                <div class="h-full bg-sage-600 rounded-full transition-all duration-1000 ease-out relative"
                    style="width: {{ ($kpi['kamar_terisi'] / max($kpi['kamar_total'], 1)) * 100 }}%">
                    <div class="absolute inset-0 bg-white/20 w-full animate-[shimmer_2s_infinite]"></div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.penghuni.index') }}"
            class="group animate-in block bg-white rounded-2xl p-5 shadow-sm border border-cream-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-out"
            style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-cream-500">Penghuni Aktif</p>
                <div
                    class="w-10 h-10 rounded-xl bg-terracotta-100 text-terracotta-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    @include('partial.icons.users', ['class' => 'w-5 h-5'])
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-cream-900 tracking-tight">{{ $kpi['penghuni_aktif'] }}</p>
            @if ($kpi['penghuni_bulan_ini'] > 0)
                <p
                    class="mt-3 inline-flex items-center gap-1 bg-terracotta-50 border border-terracotta-100 text-[11px] font-bold text-terracotta-700 px-2.5 py-1 rounded-md">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                    </svg>
                    +{{ $kpi['penghuni_bulan_ini'] }} bulan ini
                </p>
            @else
                <p class="mt-3 text-[11px] font-medium text-cream-500">Stabil bulan ini</p>
            @endif
        </a>

        <a href="{{ route('admin.tagihan.index') }}"
            class="group animate-in block bg-white rounded-2xl p-5 shadow-sm border border-cream-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-out"
            style="animation-delay: 0.15s">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-cream-500">Tagihan Lunas</p>
                <div
                    class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    @include('partial.icons.receipt', ['class' => 'w-5 h-5'])
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-cream-900 tracking-tight">
                {{ $kpi['tagihan_lunas'] }} <span class="text-base font-medium text-cream-400">/
                    {{ $kpi['tagihan_total'] }}</span>
            </p>
            <div class="mt-4 flex items-center gap-3">
                <div class="flex-1 h-1.5 bg-cream-100 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 rounded-full transition-all duration-1000 ease-out"
                        style="width: {{ ($kpi['tagihan_lunas'] / max($kpi['tagihan_total'], 1)) * 100 }}%"></div>
                </div>
                <p class="text-[10px] font-bold text-cream-500 shrink-0">Bulan Ini</p>
            </div>
        </a>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="animate-in" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between mb-3.5">
                <h2 class="flex items-center gap-2 text-base font-bold text-cream-900">
                    Perlu Ditindaklanjuti
                </h2>
            </div>

            @if (count($perluTindakan) > 0)
                <div class="bg-white rounded-2xl border border-cream-200 overflow-hidden shadow-sm flex flex-col">
                    @foreach ($perluTindakan as $item)
                        @php
                            [$iconBg, $icon, $borderClass, $badgeClass] = match ($item['type']) {
                                'telat' => [
                                    'bg-terracotta-100 text-terracotta-600',
                                    'warning',
                                    'border-l-4 border-l-terracotta-500',
                                    'bg-terracotta-500 text-white animate-pulse shadow-sm shadow-terracotta-500/30',
                                ],
                                'keluhan' => [
                                    'bg-terracotta-100 text-terracotta-600',
                                    'megaphone',
                                    'border-l-4 border-l-terracotta-500',
                                    'bg-terracotta-500 text-white shadow-sm',
                                ],
                                'verifikasi' => [
                                    'bg-amber-100 text-amber-600',
                                    'receipt',
                                    'border-l-4 border-l-amber-400',
                                    'bg-amber-500 text-white shadow-sm',
                                ],
                                'kamar_kosong' => [
                                    'bg-sage-100 text-sage-700',
                                    'door',
                                    'border-l-4 border-l-sage-400',
                                    'bg-sage-600 text-white shadow-sm',
                                ],
                                default => [
                                    'bg-cream-100 text-cream-600',
                                    'warning',
                                    'border-l-4 border-l-cream-300',
                                    'bg-cream-500 text-white',
                                ],
                            };
                        @endphp
                        <a href="{{ route($item['route']) }}"
                            class="group flex items-center gap-3.5 px-5 py-3.5 hover:bg-cream-50 transition-all duration-200 ease-out {{ $borderClass }} border-b border-b-cream-100 last:border-b-0">

                            <div
                                class="shrink-0 w-9 h-9 rounded-xl {{ $iconBg }} flex items-center justify-center group-hover:scale-110 transition-transform">
                                @include('partial.icons.' . $icon, ['class' => 'w-4 h-4'])
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-cream-900 group-hover:text-sage-700 transition-colors">
                                    {{ $item['label'] }}</p>
                                <p class="text-xs font-medium text-cream-500 truncate mt-0.5">{{ $item['meta'] }}</p>
                            </div>

                            @if (isset($item['count']) && $item['count'] > 0)
                                <span
                                    class="shrink-0 {{ $badgeClass }} text-[11px] font-bold rounded-full min-w-[22px] h-[22px] px-1.5 flex items-center justify-center">
                                    {{ $item['count'] }}
                                </span>
                            @else
                                <span
                                    class="shrink-0 text-cream-300 group-hover:text-sage-500 transition-colors group-hover:translate-x-1 duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <div
                    class="bg-white rounded-2xl border border-dashed border-cream-300 p-8 text-center flex flex-col items-center justify-center">
                    <div class="w-12 h-12 bg-sage-50 text-sage-500 rounded-full flex items-center justify-center mb-3">
                        @include('partial.icons.shield', ['class' => 'w-6 h-6'])
                    </div>
                    <p class="text-sm font-bold text-cream-900">Semua Terkendali!</p>
                    <p class="text-xs text-cream-500 mt-1">Tidak ada tugas mendesak yang perlu ditindaklanjuti saat ini.</p>
                </div>
            @endif
        </div>

        <div class="animate-in" style="animation-delay: 0.25s">
            <div class="flex items-center justify-between mb-3.5">
                <h2 class="flex items-center gap-2 text-base font-bold text-cream-900">
                    Aktivitas Terbaru
                </h2>
            </div>

            @if (count($aktivitas) > 0)
                <div class="bg-white rounded-2xl border border-cream-200 p-5 shadow-sm">
                    <div
                        class="relative before:absolute before:inset-y-2 before:left-[15px] before:w-px before:bg-cream-200 space-y-5">
                        @foreach ($aktivitas as $item)
                            <div class="relative flex gap-3.5 group">
                                <div
                                    class="relative z-10 shrink-0 w-8 h-8 rounded-full bg-cream-100 text-cream-600 ring-4 ring-white flex items-center justify-center group-hover:bg-sage-100 group-hover:text-sage-700 transition-colors">
                                    @include('partial.icons.clock', ['class' => 'w-3.5 h-3.5'])
                                </div>
                                <div class="pt-1.5 min-w-0 flex-1">
                                    <p class="text-[13px] text-cream-800 leading-relaxed">
                                        <strong class="font-bold text-cream-900">{{ $item['name'] }}</strong>
                                        {{ $item['action'] }}
                                    </p>
                                    <p class="text-[11px] font-medium text-cream-400 mt-1 flex items-center gap-1">
                                        {{ $item['waktu'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-cream-200 p-8 text-center shadow-sm">
                    <p class="text-xs text-cream-500">Belum ada aktivitas tercatat.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function generateTagihanFromDashboard() {
            const btn = document.getElementById('btn-generate-dash');
            const statusEl = document.getElementById('generate-dash-status');
            btn.disabled = true;
            btn.classList.add('opacity-60');
            statusEl.classList.add('hidden');

            try {
                const resPenghuni = await fetch('{{ route('admin.tagihan.daftarpenghuni') }}', {
                    headers: {
                        'Accept': 'application/json'
                    },
                });
                const jsonPenghuni = await resPenghuni.json();
                const penghuniAktif = jsonPenghuni.data;

                let dibuat = 0,
                    dilewati = 0;
                const bulanIni = new Date().toISOString().slice(0, 7);

                for (const p of penghuniAktif) {
                    const res = await fetch('{{ route('admin.tagihan.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            penghunian_id: p.id,
                            bulan_tagihan: bulanIni,
                            jumlah: p.kamar?.harga ?? 0,
                        }),
                    });
                    if (res.status === 201) dibuat++;
                    else dilewati++;
                }

                statusEl.textContent = `Selesai! ${dibuat} tagihan baru, ${dilewati} sudah ada sebelumnya.`;
                statusEl.classList.remove('hidden', 'text-terracotta-600');
                statusEl.classList.add('text-sage-700');
                btn.disabled = false;
                btn.classList.remove('opacity-60');
            } catch (err) {
                statusEl.textContent = 'Gagal men-generate tagihan. Periksa koneksi.';
                statusEl.classList.remove('hidden', 'text-sage-700');
                statusEl.classList.add('text-terracotta-600');
                btn.disabled = false;
                btn.classList.remove('opacity-60');
            }
        }
    </script>
@endpush
