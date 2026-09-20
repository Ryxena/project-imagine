@extends('layout.penghuni.PenghuniDashboardLayout')

@section('page-title', 'Tagihan')
@section('page-subtitle', 'Kelola pembayaran sewa kamar Anda.')

@section('content')
    @php
        $tagihanDitolak = $aktif->firstWhere('status_pembayaran', 'ditolak');
    @endphp

    @if ($aktif->count() > 0)
        <div class="animate-in grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">
            <div class="space-y-4">
                @foreach ($aktif as $t)
                    @php
                        $bulanLabel = \Carbon\Carbon::createFromFormat('Y-m', $t->bulan_tagihan)->translatedFormat(
                            'F Y',
                        );
                        $perluUpload = $t->status_pembayaran !== 'menunggu_verifikasi';
                    @endphp

                    <div class="bg-white rounded-xl border border-cream-200 shadow-sm p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                @if ($t->status_pembayaran === 'menunggu_verifikasi')
                                    <span
                                        class="bg-amber-100 text-amber-600 text-[10px] font-semibold px-2.5 py-1 rounded-full">Menunggu
                                        Verifikasi</span>
                                @elseif ($t->status_pembayaran === 'ditolak')
                                    <span
                                        class="bg-terracotta-100 text-terracotta-600 text-[10px] font-semibold px-2.5 py-1 rounded-full">Ditolak</span>
                                @else
                                    <span
                                        class="bg-cream-100 text-cream-600 text-[10px] font-semibold px-2.5 py-1 rounded-full">Belum
                                        Dibayar</span>
                                @endif
                                <p class="mt-2 text-base font-semibold text-cream-900">Tagihan {{ $bulanLabel }}</p>
                                <p class="text-xs text-cream-600">Kamar {{ $t->penghunian->kamar->nomor_kamar ?? '-' }}</p>
                            </div>
                            <p class="text-lg font-bold text-cream-900">Rp{{ number_format($t->jumlah, 0, ',', '.') }}</p>
                        </div>

                        @if (!$perluUpload)
                            <p class="mt-4 text-xs text-cream-600 bg-cream-50 p-3 rounded-lg border border-cream-100">Bukti
                                transfer sedang ditinjau oleh pengelola.</p>
                        @else
                            <div class="mt-4" id="upload-container-{{ $t->id }}">
                                <label id="dropzone-{{ $t->id }}"
                                    class="block w-full border-2 border-dashed border-cream-300 rounded-xl px-4 py-6 text-center cursor-pointer hover:border-sage-400 hover:bg-cream-50 transition-colors duration-150 group">
                                    <input type="file" id="file-input-{{ $t->id }}" class="hidden"
                                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                                        onchange="handleFileSelect({{ $t->id }}, this)">
                                    <div
                                        class="w-9 h-9 mx-auto rounded-full bg-cream-100 text-cream-600 flex items-center justify-center mb-2 group-hover:text-sage-600 group-hover:bg-sage-100 transition-colors">
                                        @include('partial.icons.receipt', ['class' => 'w-4 h-4'])
                                    </div>
                                    <p class="text-xs font-medium text-sage-700">
                                        {{ $t->status_pembayaran === 'ditolak' ? 'Upload Ulang Bukti Transfer' : 'Upload Bukti Transfer' }}
                                    </p>
                                    <p class="mt-0.5 text-[11px] text-cream-500">Format JPG, PNG, atau PDF maks 2MB</p>
                                </label>

                                <div id="preview-state-{{ $t->id }}"
                                    class="hidden w-full border border-sage-200 bg-sage-50/50 rounded-xl p-4">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div
                                            class="w-8 h-8 rounded-full bg-sage-200 text-sage-700 flex items-center justify-center shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[13px] font-bold text-sage-900 truncate"
                                                id="file-name-{{ $t->id }}"></p>
                                            <p class="text-[11px] text-sage-600">Siap diunggah</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button onclick="cancelUpload({{ $t->id }})"
                                            class="flex-1 py-2 text-[12px] font-semibold text-cream-700 bg-white border border-cream-300 rounded-lg hover:bg-cream-50 transition-colors">Batal</button>
                                        <button onclick="submitUpload({{ $t->id }})"
                                            id="btn-submit-{{ $t->id }}"
                                            class="flex-1 py-2 text-[12px] font-semibold text-white bg-sage-700 rounded-lg hover:bg-sage-800 transition-colors flex justify-center items-center gap-1.5 shadow-sm">Kirim
                                            Bukti</button>
                                    </div>
                                </div>

                                <p id="upload-error-{{ $t->id }}"
                                    class="hidden mt-3 text-xs text-terracotta-600 bg-terracotta-50 p-2.5 rounded-lg border border-terracotta-100">
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="space-y-4">
                @if ($tagihanDitolak)
                    @php
                        $bulanDitolak = \Carbon\Carbon::createFromFormat(
                            'Y-m',
                            $tagihanDitolak->bulan_tagihan,
                        )->translatedFormat('F Y');

                        $alasan =
                            $tagihanDitolak->pembayaran
                                ->where('status_verifikasi', 'failed')
                                ->sortByDesc('created_at')
                                ->first()?->alasan_penolakan ?? 'Bukti tidak valid.';
                    @endphp
                    <div class="bg-terracotta-200 border border-terracotta-200 rounded-xl p-4">
                        <p class="flex items-center gap-1.5 text-sm font-semibold text-terracotta-700">
                            @include('partial.icons.warning', ['class' => 'w-4 h-4'])
                            Pembayaran Ditolak
                        </p>
                        <p class="mt-2 text-xs font-medium text-cream-900">Tagihan {{ $bulanDitolak }}</p>
                        <p class="mt-1 text-xs text-terracotta-700 leading-relaxed">Alasan: {{ $alasan }}</p>
                    </div>
                @endif

                <div class="bg-cream-100 border border-cream-200 rounded-xl p-4">
                    <p class="text-xs font-semibold text-cream-900 mb-2.5">Informasi Rekening</p>
                    <div class="space-y-2">
                        <div class="bg-white rounded-lg px-3 py-2 border border-cream-200">
                            <p class="text-[11px] font-medium text-cream-700">BCA — a.n. NgekostYuk</p>
                            <p class="text-sm font-semibold text-cream-900 mt-0.5">1234 5678 90</p>
                        </div>
                        <div class="bg-white rounded-lg px-3 py-2 border border-cream-200">
                            <p class="text-[11px] font-medium text-cream-700">Mandiri — a.n. NgekostYuk</p>
                            <p class="text-sm font-semibold text-cream-900 mt-0.5">098 765 4321</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl border border-cream-200 px-4 py-8 text-center shadow-sm animate-in">
            <p class="text-sm text-cream-600">Tidak ada tagihan yang perlu ditindaklanjuti. Semua beres!</p>
        </div>
    @endif

    <div class="mt-6 animate-in" style="animation-delay: 0.1s">
        <p class="text-base font-semibold text-cream-900 mb-3">Riwayat Pembayaran</p>

        <div id="riwayat-tagihan-grid"
            class="bg-white rounded-xl border border-cream-200 shadow-sm divide-y divide-cream-200">

            @if ($riwayat->count() > 0)
                @foreach ($riwayat as $t)
                    @php
                        $bulanLabel = \Carbon\Carbon::createFromFormat('Y-m', $t->bulan_tagihan)->translatedFormat(
                            'F Y',
                        );
                        $bayar = $t->pembayaran->firstWhere('status_verifikasi', 'success');
                    @endphp
                    <div class="riwayat-tagihan-item flex items-center gap-4 px-5 py-3.5">
                        <div
                            class="w-8 h-8 rounded-full bg-sage-100 text-sage-700 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-cream-900">{{ $bulanLabel }}</p>
                            @if ($bayar)
                                <p class="text-[11px] text-cream-600">Dibayar pada
                                    {{ \Carbon\Carbon::parse($bayar->tanggal_pembayaran)->translatedFormat('d M Y') }}</p>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-cream-900">Rp{{ number_format($t->jumlah, 0, ',', '.') }}</p>
                        <span
                            class="bg-sage-100 text-sage-700 text-[10px] font-semibold px-2.5 py-1 rounded-full">Lunas</span>
                    </div>
                @endforeach
            @else
                <div class="px-5 py-8 text-center">
                    <p class="text-sm text-cream-600">Belum ada riwayat pembayaran.</p>
                </div>
            @endif

        </div>

        @if ($riwayat->hasPages())
            <div class="mt-5">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function handleFileSelect(tagihanId, input) {
            const file = input.files[0];
            if (!file) return;

            document.getElementById(`file-name-${tagihanId}`).textContent = file.name;

            document.getElementById(`dropzone-${tagihanId}`).classList.add('hidden');
            document.getElementById(`preview-state-${tagihanId}`).classList.remove('hidden');
            document.getElementById(`upload-error-${tagihanId}`).classList.add('hidden');
        }

        function cancelUpload(tagihanId) {
            document.getElementById(`file-input-${tagihanId}`).value = '';

            document.getElementById(`dropzone-${tagihanId}`).classList.remove('hidden');
            document.getElementById(`preview-state-${tagihanId}`).classList.add('hidden');
            document.getElementById(`upload-error-${tagihanId}`).classList.add('hidden');
        }

        async function submitUpload(tagihanId) {
            const input = document.getElementById(`file-input-${tagihanId}`);
            const file = input.files[0];
            if (!file) return;

            const errorEl = document.getElementById(`upload-error-${tagihanId}`);
            const submitBtn = document.getElementById(`btn-submit-${tagihanId}`);

            errorEl.classList.add('hidden');

            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg> Mengunggah...
            `;
            submitBtn.classList.add('opacity-80', 'cursor-not-allowed');

            const formData = new FormData();
            formData.append('tagihan_id', tagihanId);
            formData.append('bukti_pembayaran', file);
            formData.append('tanggal_pembayaran', new Date().toISOString().slice(0, 10));

            try {
                const res = await fetch('{{ route('penghuni.pembayaran.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                });
                const json = await res.json();

                if (!res.ok) {
                    throw new Error(json.message || 'Gagal mengunggah bukti pembayaran.');
                }

                window.location.reload();
            } catch (err) {
                errorEl.textContent = err.message || 'Terjadi kesalahan jaringan.';
                errorEl.classList.remove('hidden');

                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                submitBtn.classList.remove('opacity-80', 'cursor-not-allowed');
            }
        }
    </script>
@endpush
