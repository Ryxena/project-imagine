@extends('layout.admin.AdminDashboardLayout')

@section('page-title', 'Verifikasi Pembayaran')
@section('page-subtitle', 'Tinjau bukti pembayaran yang masuk.')

@section('content')
    @php
        $pending = $pembayarans->where('status_verifikasi', 'pending')->values();
        $riwayat = $pembayarans->whereIn('status_verifikasi', ['success', 'failed'])->values();
    @endphp

    <div class="flex items-center gap-6 border-b border-cream-200 animate-in">
        <button data-tab="pending" onclick="switchTab('pending')"
            class="tab-btn pb-3 text-sm font-semibold text-sage-700 border-b-2 border-sage-700 flex items-center gap-2">
            Menunggu Verifikasi
            @if ($pending->count() > 0)
                <span
                    class="bg-terracotta-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center">{{ $pending->count() }}</span>
            @endif
        </button>
        <button data-tab="riwayat" onclick="switchTab('riwayat')"
            class="tab-btn pb-3 text-sm font-medium text-cream-600 border-b-2 border-transparent">
            Riwayat
        </button>
    </div>

    <div id="panel-pending" class="mt-5 space-y-3 animate-in" style="animation-delay: 0.05s">
        @forelse ($pending as $p)
            @php
                $nama = $p->tagihan->penghunian->user->name ?? '-';
                $kamar = $p->tagihan->penghunian->kamar->nomor_kamar ?? '-';
                $bulanLabel = \Carbon\Carbon::createFromFormat('Y-m', $p->tagihan->bulan_tagihan)->translatedFormat(
                    'F Y',
                );
            @endphp
            <div class="bg-white rounded-xl border border-cream-200 shadow-sm p-4 flex items-center gap-4 flex-wrap">
                <div
                    class="w-11 h-11 rounded-lg bg-slateblue-100 text-slateblue-600 flex items-center justify-center shrink-0">
                    @include('partial.icons.receipt', ['class' => 'w-5 h-5'])
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-cream-900">
                        {{ $nama }}
                        <span
                            class="ml-1 bg-cream-100 text-cream-600 text-[10px] font-semibold px-2 py-0.5 rounded-full">Kamar
                            {{ $kamar }}</span>
                    </p>
                    <p class="text-[11px] text-cream-600 mt-0.5">Pembayaran Kos &bull; Bulan {{ $bulanLabel }}</p>
                    <p class="text-sm font-bold text-cream-900 mt-1">Rp{{ number_format($p->tagihan->jumlah, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button onclick="openDetailModal({{ $p->id }})"
                        class="flex items-center gap-1.5 text-xs font-medium text-sage-700 bg-sage-100 px-3.5 py-2 rounded-lg hover:bg-sage-200 transition-colors duration-150">
                        Lihat Bukti
                    </button>
                    <button onclick="openRejectModal({{ $p->id }})"
                        class="text-xs font-medium text-terracotta-600 bg-terracotta-50 px-3.5 py-2 rounded-lg hover:bg-terracotta-100 transition-colors duration-150">
                        Tolak
                    </button>
                    <button onclick="openAcceptModal({{ $p->id }})"
                        class="text-xs font-medium text-white bg-sage-700 px-3.5 py-2 rounded-lg hover:bg-sage-800 transition-colors duration-150">
                        Terima
                    </button>
                </div>
            </div>
        @empty
            <div class="px-5 py-10 text-center">

                <div class="mx-auto w-10 h-10 rounded-full bg-sage-50 text-sage-600 flex items-center justify-center">
                    @include('partial.icons.shield', ['class' => 'w-5 h-5'])
                </div>

                <p class="mt-3 text-sm font-medium text-cream-800">
                    Tidak ada pembayaran menunggu verifikasi
                </p>

                <p class="mt-1 text-xs text-cream-600 max-w-xs mx-auto leading-relaxed">
                    Semua pembayaran sudah diperiksa. Pengajuan baru akan muncul di sini.
                </p>

            </div>
        @endforelse
    </div>

    <div id="panel-riwayat" class="hidden mt-5 space-y-3 animate-in" style="animation-delay: 0.05s">
        @forelse ($riwayat as $p)
            @php
                $nama = $p->tagihan->penghunian->user->name ?? '-';
                $kamar = $p->tagihan->penghunian->kamar->nomor_kamar ?? '-';
                $isSuccess = $p->status_verifikasi === 'success';
            @endphp
            <div class="riwayat-item bg-white rounded-xl border border-cream-200 shadow-sm p-4 flex items-center gap-4">
                <div
                    class="w-11 h-11 rounded-lg {{ $isSuccess ? 'bg-sage-100 text-sage-700' : 'bg-terracotta-100 text-terracotta-600' }} flex items-center justify-center shrink-0">
                    @include('partial.icons.receipt', ['class' => 'w-5 h-5'])
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-cream-900">{{ $nama }} <span
                            class="ml-1 bg-cream-100 text-cream-600 text-[10px] font-semibold px-2 py-0.5 rounded-full">Kamar
                            {{ $kamar }}</span></p>
                    <p class="text-[11px] text-cream-600 mt-0.5">Rp{{ number_format($p->tagihan->jumlah, 0, ',', '.') }}
                    </p>
                    @if (!$isSuccess && $p->alasan_penolakan)
                        <p class="text-[11px] text-terracotta-600 mt-0.5">Alasan: {{ $p->alasan_penolakan }}</p>
                    @endif
                </div>
                <span
                    class="shrink-0 {{ $isSuccess ? 'bg-sage-100 text-sage-700' : 'bg-terracotta-100 text-terracotta-600' }} text-[10px] font-semibold px-3 py-1.5 rounded-full">
                    {{ $isSuccess ? 'Diterima' : 'Ditolak' }}
                </span>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-cream-200 px-4 py-8 text-center shadow-sm">
                <p class="text-sm text-cream-600">Belum ada riwayat verifikasi.</p>
            </div>
        @endforelse

        <nav id="riwayat-pagination" role="navigation" class="hidden flex items-center justify-center pt-4 px-1">
            <div id="riwayat-pagination-container" class="flex items-center gap-1.5">
            </div>
        </nav>
    </div>

    <div id="modal-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeDetailModal()"></div>
        <div
            class="relative bg-white rounded-2xl shadow-lg w-full max-w-2xl grid grid-cols-1 md:grid-cols-2 overflow-hidden animate-in">
            <div class="bg-cream-100 flex items-center justify-center p-4 min-h-[280px]">
                <div id="bukti-preview"></div>
            </div>
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-cream-900">Verifikasi Pembayaran</h3>
                        <p class="mt-1 text-xs text-cream-600">Periksa kesesuaian bukti transfer dengan detail tagihan.</p>
                    </div>
                    <button onclick="closeDetailModal()"
                        class="text-cream-400 hover:text-cream-700 transition-colors duration-150 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-4 bg-cream-50 rounded-xl divide-y divide-cream-200 text-sm">
                    <div class="flex justify-between px-4 py-3">
                        <span class="text-cream-500">Nama Penghuni</span>
                        <span id="detail-nama" class="font-semibold text-cream-900"></span>
                    </div>
                    <div class="flex justify-between px-4 py-3">
                        <span class="text-cream-500">Periode</span>
                        <span id="detail-periode" class="font-medium text-cream-900"></span>
                    </div>
                    <div class="flex justify-between px-4 py-3">
                        <span class="text-cream-500">Total Tagihan</span>
                        <span id="detail-total" class="font-bold text-cream-900"></span>
                    </div>
                </div>

                <p id="detail-error" class="hidden mt-3 text-xs text-terracotta-600"></p>

                <div class="mt-5 space-y-2">
                    <button id="btn-verifikasi" onclick="openAcceptModal(currentPembayaranId); closeDetailModal();"
                        class="w-full bg-sage-700 text-white text-sm font-medium py-2.5 rounded-full hover:bg-sage-800 active:scale-[0.98] transition-all duration-150 ease-out">
                        Verifikasi &amp; Tandai Lunas
                    </button>
                    <button onclick="openRejectModal(currentPembayaranId); closeDetailModal();"
                        class="w-full text-terracotta-600 border border-terracotta-200 text-sm font-medium py-2.5 rounded-full hover:bg-terracotta-50 transition-colors duration-150">
                        Tolak Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-reject" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeRejectModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-md animate-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200">
                <h3 class="text-base font-semibold text-terracotta-700 flex items-center gap-2">
                    @include('partial.icons.warning', ['class' => 'w-5 h-5'])
                    Tolak Pembayaran
                </h3>
                <button onclick="closeRejectModal()"
                    class="text-cream-400 hover:text-cream-700 transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5">
                <div class="bg-terracotta-50 text-terracotta-700 text-xs rounded-lg px-3.5 py-3">
                    Alasan ini akan terlihat oleh penghuni agar mereka dapat mengunggah bukti yang benar.
                </div>

                <label class="block mt-4 text-xs font-medium text-cream-700 mb-1.5">Alasan Penolakan <span
                        class="text-terracotta-600">*</span></label>
                <textarea id="reject-reason" rows="3" placeholder="Contoh: Bukti transfer buram atau jumlah tidak sesuai..."
                    class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-terracotta-400 focus:border-terracotta-400 transition-colors duration-150 resize-none"></textarea>
                <p id="reject-error" class="hidden mt-2 text-xs text-terracotta-600"></p>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
                <button onclick="closeRejectModal()"
                    class="text-sm font-medium text-cream-600 hover:text-cream-900 transition-colors duration-150">Batal</button>
                <button onclick="submitReject()"
                    class="bg-terracotta-600 text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-terracotta-700 active:scale-[0.98] transition-all duration-150 ease-out">Tolak
                    Sekarang</button>
            </div>
        </div>
    </div>

    <div id="modal-accept" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeAcceptModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-sm animate-in">
            <div class="px-6 py-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-sage-100 text-sage-700 flex items-center justify-center shrink-0">
                        @include('partial.icons.shield', ['class' => 'w-5 h-5'])
                    </div>
                    <h3 class="text-base font-semibold text-cream-900">Terima Pembayaran?</h3>
                </div>
                <p class="mt-3 text-sm text-cream-600">
                    Tagihan <span id="accept-nama" class="font-medium text-cream-900"></span> akan ditandai
                    <strong>lunas</strong>. Aksi ini tidak dapat dibatalkan.
                </p>
                <p id="accept-error" class="hidden mt-2 text-xs text-terracotta-600"></p>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
                <button onclick="closeAcceptModal()"
                    class="text-sm font-medium text-cream-600 hover:text-cream-900 transition-colors duration-150">Batal</button>
                <button onclick="submitAccept()"
                    class="bg-sage-700 text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-sage-800 active:scale-[0.98] transition-all duration-150 ease-out">Ya,
                    Terima</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const pembayaranData = @json($pembayarans->keyBy('id'));
        let currentPembayaranId = null;

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                const active = btn.dataset.tab === tab;
                btn.classList.toggle('text-sage-700', active);
                btn.classList.toggle('border-sage-700', active);
                btn.classList.toggle('text-cream-600', !active);
                btn.classList.toggle('border-transparent', !active);
            });
            document.getElementById('panel-pending').classList.toggle('hidden', tab !== 'pending');
            document.getElementById('panel-riwayat').classList.toggle('hidden', tab !== 'riwayat');
            if (tab === 'riwayat') renderPagination();
        }

        const PAGE_SIZE = 5;
        let currentPage = 1;

        function renderPagination() {
            const items = document.querySelectorAll('.riwayat-item');
            const totalPages = Math.max(1, Math.ceil(items.length / PAGE_SIZE));

            if (currentPage > totalPages) currentPage = totalPages;

            // Atur visibilitas data item
            items.forEach((item, i) => {
                const page = Math.floor(i / PAGE_SIZE) + 1;
                item.style.display = (page === currentPage) ? '' : 'none';
            });

            const paginationNav = document.getElementById('riwayat-pagination');
            const container = document.getElementById('riwayat-pagination-container');

            if (totalPages <= 1) {
                paginationNav.classList.add('hidden');
                return;
            } else {
                paginationNav.classList.remove('hidden');
            }

            let html = '';

            // Tombol Previous
            const prevDisabled = currentPage === 1;
            html += `
                <button onclick="changePage(-1)" ${prevDisabled ? 'disabled' : ''}
                    class="inline-flex items-center justify-center w-8 h-8 rounded-xl transition-all duration-150 ${
                        prevDisabled 
                            ? 'text-cream-300 bg-cream-50/60 border border-cream-200/60 cursor-not-allowed shadow-none' 
                            : 'text-cream-700 bg-white border border-cream-200 hover:bg-sage-50 hover:border-sage-300 hover:text-sage-700 shadow-2xs cursor-pointer'
                    }">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </button>
            `;

            // Tombol Nomor Halaman
            for (let i = 1; i <= totalPages; i++) {
                if (i === currentPage) {
                    html += `
                        <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-sage-700 border border-sage-700 rounded-xl shadow-xs cursor-default">
                            ${i}
                        </span>
                    `;
                } else {
                    html += `
                        <button onclick="goToPage(${i})"
                            class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-cream-700 bg-white border border-cream-200 rounded-xl hover:bg-sage-50 hover:border-sage-300 hover:text-sage-700 transition-all duration-150 shadow-2xs">
                            ${i}
                        </button>
                    `;
                }
            }

            // Tombol Next
            const nextDisabled = currentPage === totalPages;
            html += `
                <button onclick="changePage(1)" ${nextDisabled ? 'disabled' : ''}
                    class="inline-flex items-center justify-center w-8 h-8 rounded-xl transition-all duration-150 ${
                        nextDisabled 
                            ? 'text-cream-300 bg-cream-50/60 border border-cream-200/60 cursor-not-allowed shadow-none' 
                            : 'text-cream-700 bg-white border border-cream-200 hover:bg-sage-50 hover:border-sage-300 hover:text-sage-700 shadow-2xs cursor-pointer'
                    }">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            `;

            container.innerHTML = html;
        }

        function changePage(delta) {
            currentPage += delta;
            renderPagination();
        }

        function goToPage(page) {
            currentPage = page;
            renderPagination();
        }

        document.addEventListener('DOMContentLoaded', renderPagination);

        function openDetailModal(id) {
            currentPembayaranId = id;
            const p = pembayaranData[id];
            const nama = p.tagihan.penghunian?.user?.name ?? '-';
            const bulan = p.tagihan.bulan_tagihan;
            const [tahun, bln] = bulan.split('-');
            const bulanNama = new Date(tahun, bln - 1).toLocaleDateString('id-ID', {
                month: 'long',
                year: 'numeric'
            });

            document.getElementById('detail-nama').textContent = nama;
            document.getElementById('detail-periode').textContent = bulanNama;
            document.getElementById('detail-total').textContent = 'Rp' + Number(p.tagihan.jumlah).toLocaleString('id-ID');
            document.getElementById('detail-error').classList.add('hidden');

            const preview = document.getElementById('bukti-preview');
            const url = `/storage/${p.bukti_pembayaran}`;
            if (p.bukti_pembayaran.toLowerCase().endsWith('.pdf')) {
                preview.innerHTML =
                    `<a href="${url}" target="_blank" class="text-sm text-sage-700 underline">Buka bukti pembayaran (PDF)</a>`;
            } else {
                preview.innerHTML = `<img src="${url}" alt="Bukti pembayaran" class="max-h-80 rounded-lg object-contain">`;
            }

            document.getElementById('modal-detail').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('modal-detail').classList.add('hidden');
        }

        async function verifikasi(id, status, alasan = null) {
            const payload = {
                status_verifikasi: status
            };
            if (alasan) payload.alasan_penolakan = alasan;

            const res = await fetch(`/admin/verifikasipembayaran/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(payload),
            });
            const json = await res.json();
            return {
                ok: res.ok && json.success,
                message: json.message
            };
        }

        function openAcceptModal(id) {
            currentPembayaranId = id;
            const p = pembayaranData[id];
            const nama = p.tagihan.penghunian?.user?.name ?? '-';
            document.getElementById('accept-nama').textContent = nama;
            document.getElementById('accept-error').classList.add('hidden');
            document.getElementById('modal-accept').classList.remove('hidden');
        }

        function closeAcceptModal() {
            document.getElementById('modal-accept').classList.add('hidden');
        }

        async function submitAccept() {
            const errorEl = document.getElementById('accept-error');
            errorEl.classList.add('hidden');

            const result = await verifikasi(currentPembayaranId, 'success');
            if (!result.ok) {
                errorEl.textContent = result.message || 'Gagal memverifikasi pembayaran.';
                errorEl.classList.remove('hidden');
                return;
            }
            window.location.reload();
        }

        function openRejectModal(id) {
            currentPembayaranId = id;
            document.getElementById('reject-reason').value = '';
            document.getElementById('reject-error').classList.add('hidden');
            document.getElementById('modal-reject').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('modal-reject').classList.add('hidden');
        }

        async function submitReject() {
            const reason = document.getElementById('reject-reason').value.trim();
            const errorEl = document.getElementById('reject-error');

            if (!reason) {
                errorEl.textContent = 'Alasan penolakan wajib diisi.';
                errorEl.classList.remove('hidden');
                return;
            }

            const result = await verifikasi(currentPembayaranId, 'failed', reason);
            if (!result.ok) {
                errorEl.textContent = result.message || 'Gagal menolak pembayaran.';
                errorEl.classList.remove('hidden');
                return;
            }
            window.location.reload();
        }
    </script>
@endpush
