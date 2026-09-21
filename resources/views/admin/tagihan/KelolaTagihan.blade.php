@extends('layout.admin.AdminDashboardLayout')

@section('page-title', 'Tagihan')
@section('page-subtitle', 'Kelola tagihan bulanan dan riwayat pembayaran penghuni.')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-in">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-cream-200">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-sage-100 text-sage-700 flex items-center justify-center shrink-0">
                    @include('partial.icons.receipt', ['class' => 'w-4 h-4'])
                </div>
                <p class="text-[11px] font-medium text-cream-600 uppercase tracking-wide">Total Pendapatan</p>
            </div>
            <p class="mt-2.5 text-xl font-bold text-cream-900">Rp {{ number_format($kpi['total_pendapatan'], 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-terracotta-200">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-lg bg-terracotta-100 text-terracotta-600 flex items-center justify-center shrink-0">
                    @include('partial.icons.warning', ['class' => 'w-4 h-4'])
                </div>
                <p class="text-[11px] font-medium text-cream-600 uppercase tracking-wide">Belum Dibayar</p>
            </div>
            <p class="mt-2.5 text-xl font-bold text-cream-900">Rp {{ number_format($kpi['belum_dibayar'], 0, ',', '.') }}
            </p>
            <p class="mt-1 text-[11px] text-terracotta-600">{{ $kpi['pending_count'] }} tagihan tertunda</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-cream-200">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-sage-100 text-sage-700 flex items-center justify-center shrink-0">
                    @include('partial.icons.shield', ['class' => 'w-4 h-4'])
                </div>
                <p class="text-[11px] font-medium text-cream-600 uppercase tracking-wide">Tagihan Lunas</p>
            </div>
            <p class="mt-2.5 text-xl font-bold text-cream-900">{{ $kpi['lunas_bulan_ini'] }} <span
                    class="text-sm font-normal text-cream-600">/ {{ $kpi['total_bulan_ini'] }}</span></p>
            <p class="mt-1 text-[11px] text-cream-600">Bulan ini</p>
        </div>
    </div>

    <div class="mt-5 flex flex-col md:flex-row md:items-center justify-between gap-4 animate-in"
        style="animation-delay: 0.05s">
        <button onclick="generateTagihan()" id="btn-generate"
            class="w-full md:w-auto flex justify-center items-center gap-2 bg-sage-700 text-white text-xs font-medium px-5 py-2.5 rounded-full hover:bg-sage-800 hover:shadow-md active:scale-[0.98] transition-all duration-150 ease-out shrink-0">
            + Generate Tagihan Bulan Ini
        </button>

        <div class="flex flex-col sm:flex-row w-full md:w-auto gap-2.5 shrink-0">
            <select id="filter-status"
                class="w-full sm:w-auto appearance-none cursor-pointer text-xs font-medium text-cream-700 border border-cream-300 rounded-full pl-4 pr-10 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-sage-400 bg-no-repeat bg-[position:right_12px_center] bg-[size:16px_16px]"
                style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%234b5563%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E');">
                <option value="semua">Semua Status</option>
                <option value="belum_bayar">Belum Bayar</option>
                <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                <option value="lunas">Lunas</option>
                <option value="ditolak">Ditolak</option>
            </select>

            <select id="filter-bulan"
                class="w-full sm:w-auto appearance-none cursor-pointer text-xs font-medium text-cream-700 border border-cream-300 rounded-full pl-4 pr-10 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-sage-400 bg-no-repeat bg-[position:right_12px_center] bg-[size:16px_16px]"
                style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%234b5563%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E');">
                <option value="semua">Semua Bulan</option>
                @php
                    $currentBulan = now('Asia/Jakarta')->format('Y-m');
                @endphp
                @foreach ($tagihans->pluck('bulan_tagihan')->unique()->sort()->reverse() as $bulan)
                    <option value="{{ $bulan }}" {{ $bulan === $currentBulan ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <p id="generate-status" class="hidden mt-3 text-xs text-sage-700 animate-in"></p>

    <div class="mt-5 bg-white rounded-xl border border-cream-200 shadow-sm overflow-hidden animate-in"
        style="animation-delay: 0.1s">
        <div class="px-5 py-4 border-b border-cream-200 bg-white">
            <p class="text-sm font-semibold text-cream-900">Daftar Tagihan</p>
        </div>

        <div class="overflow-x-auto">
            <div class="min-w-[650px] divide-y divide-cream-200">
                @forelse ($tagihans as $t)
                    @php
                        $nama = $t->penghunian->user->name ?? '-';
                        $image = $t->penghunian->user->image ?? null;
                        $kamar = $t->penghunian->kamar->nomor_kamar ?? '-';
                        $bulanLabel = \Carbon\Carbon::createFromFormat('Y-m', $t->bulan_tagihan)->translatedFormat(
                            'M Y',
                        );
                        [$badgeClass, $badgeLabel] = match ($t->status_pembayaran) {
                            'lunas' => ['bg-sage-100 text-sage-700', 'Lunas'],
                            'menunggu_verifikasi' => ['bg-amber-100 text-amber-600', 'Menunggu Verifikasi'],
                            'ditolak' => ['bg-terracotta-100 text-terracotta-600', 'Ditolak'],
                            default => ['bg-cream-100 text-cream-600', 'Belum Bayar'],
                        };
                    @endphp
                    <div class="tagihan-row flex items-center gap-4 px-5 py-3.5 hover:bg-cream-50 transition-colors duration-150"
                        data-status="{{ $t->status_pembayaran }}" data-bulan="{{ $t->bulan_tagihan }}">

                        @if ($image)
                            <img src="{{ asset('storage/' . $image) }}" alt="Foto {{ $nama }}"
                                class="w-9 h-9 rounded-full object-cover shrink-0 border border-sage-200 shadow-sm">
                        @else
                            <div
                                class="w-9 h-9 rounded-full bg-sage-200 text-sage-800 text-xs font-semibold flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($nama, 0, 1)) }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-cream-900 truncate">{{ $nama }}</p>
                            <p class="text-[11px] text-cream-600">Kamar {{ $kamar }} &bull; {{ $bulanLabel }}</p>
                        </div>
                        <p class="text-sm font-semibold text-cream-900 shrink-0">Rp
                            {{ number_format($t->jumlah, 0, ',', '.') }}
                        </p>
                        <span
                            class="shrink-0 {{ $badgeClass }} text-[10px] font-semibold px-3 py-1.5 rounded-full">{{ $badgeLabel }}</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center sticky left-0">
                        <p class="text-sm text-cream-600">Belum ada data tagihan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div id="filter-empty-state" class="hidden px-5 py-10 text-center border-t border-cream-200 bg-white">
            <div class="mx-auto w-10 h-10 rounded-full bg-cream-100 text-cream-500 flex items-center justify-center">
                @include('partial.icons.receipt', ['class' => 'w-5 h-5'])
            </div>
            <p class="mt-3 text-sm font-medium text-cream-800">Tidak ada tagihan yang ditemukan</p>
            <p class="mt-1 text-xs text-cream-600">Coba ubah filter atau pilih status lain untuk melihat data tagihan.</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', applyFilters);

        document.getElementById('filter-status').addEventListener('change', applyFilters);
        document.getElementById('filter-bulan').addEventListener('change', applyFilters);

        function applyFilters() {
            const status = document.getElementById('filter-status').value;
            const bulan = document.getElementById('filter-bulan').value;
            let visibleCount = 0;

            document.querySelectorAll('.tagihan-row').forEach(row => {
                const matchStatus = status === 'semua' || row.dataset.status === status;
                const matchBulan = bulan === 'semua' || row.dataset.bulan === bulan;
                const visible = matchStatus && matchBulan;

                row.style.display = visible ? '' : 'none';

                if (visible) {
                    visibleCount++;
                }
            });

            const emptyState = document.getElementById('filter-empty-state');
            emptyState.classList.toggle('hidden', visibleCount > 0);
        }

        async function generateTagihan() {
            const btn = document.getElementById('btn-generate');
            const statusEl = document.getElementById('generate-status');
            btn.disabled = true;
            btn.textContent = 'Memproses...';
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

                statusEl.textContent = `${dibuat} tagihan baru dibuat, ${dilewati} sudah ada sebelumnya.`;
                statusEl.classList.remove('hidden');

                if (dibuat > 0) {
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    btn.disabled = false;
                    btn.textContent = '+ Generate Tagihan Bulan Ini';
                }
            } catch (err) {
                statusEl.textContent = 'Terjadi kesalahan saat generate tagihan.';
                statusEl.classList.remove('hidden');
                btn.disabled = false;
                btn.textContent = '+ Generate Tagihan Bulan Ini';
            }
        }
    </script>
@endpush
