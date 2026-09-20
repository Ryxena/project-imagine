@extends('layout.penghuni.PenghuniDashboardLayout')

@section('page-title', 'Keluhan Saya')
@section('page-subtitle', 'Sampaikan kendala yang kamu alami.')

@section('content')
    <button onclick="openCreateModal()"
        class="flex items-center gap-2 bg-sage-700 text-white text-xs font-medium px-4 py-2.5 rounded-full hover:bg-sage-800 hover:shadow-md active:scale-[0.98] transition-all duration-150 ease-out animate-in">
        + Buat Keluhan Baru
    </button>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-4 items-start animate-in" style="animation-delay: 0.05s">
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-cream-200 shadow-sm p-4">
                <p class="text-sm font-semibold text-cream-900 mb-3">Ringkasan Status</p>
                <div class="space-y-2">
                    <div class="flex items-center justify-between bg-terracotta-50 rounded-lg px-3 py-2">
                        <p class="text-xs font-medium text-terracotta-700">Menunggu Proses</p>
                        <span
                            class="bg-terracotta-500 text-white text-[11px] font-semibold w-6 h-6 rounded-full flex items-center justify-center">{{ $countPending }}</span>
                    </div>
                    <div class="flex items-center justify-between bg-amber-50 rounded-lg px-3 py-2">
                        <p class="text-xs font-medium text-amber-700">Sedang Dikerjakan</p>
                        <span
                            class="bg-amber-500 text-white text-[11px] font-semibold w-6 h-6 rounded-full flex items-center justify-center">{{ $countProcess }}</span>
                    </div>
                    <div class="flex items-center justify-between bg-sage-50 rounded-lg px-3 py-2">
                        <p class="text-xs font-medium text-sage-700">Selesai</p>
                        <span
                            class="bg-sage-600 text-white text-[11px] font-semibold w-6 h-6 rounded-full flex items-center justify-center">{{ $countResolved }}</span>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-sage-800 rounded-2xl p-5 border border-sage-700 shadow-md">
                <div
                    class="absolute -top-[10%] -right-[10%] w-[70%] h-[70%] rounded-full bg-sage-500/20 blur-[50px] pointer-events-none">
                </div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <p class="text-[15px] font-bold text-white tracking-wide">Pusat Bantuan</p>
                    </div>

                    <p class="text-[13px] text-sage-100/90 leading-relaxed mb-1">Jam operasional teknisi:</p>
                    <p class="text-sm font-semibold text-white mb-5">08:00 - 17:00 WIB</p>

                    <div class="pt-4 border-t border-sage-600/50">
                        <p class="text-[11px] font-semibold text-sage-300 uppercase tracking-wide mb-2.5">Kontak Darurat
                            (Admin)</p>
                        <a href="tel:+6281234567890"
                            class="group flex items-center justify-between bg-sage-900/40 hover:bg-sage-700 border border-sage-600/50 hover:border-sage-500 rounded-xl p-3 transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-7 h-7 rounded-full bg-sage-600 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-3.5 h-3.5">
                                        <path fill-rule="evenodd"
                                            d="M2 3.5A1.5 1.5 0 0 1 3.5 2h1.148a1.5 1.5 0 0 1 1.465 1.175l.716 3.223a1.5 1.5 0 0 1-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 0 0 6.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 0 1 1.767-1.052l3.223.716A1.5 1.5 0 0 1 18 15.352V16.5a1.5 1.5 0 0 1-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 0 1 2.43 8.326 13.019 13.019 0 0 1 2 5V3.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-[13.5px] font-semibold text-white tracking-wide">+62 812-3456-7890</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col">
            <div class="space-y-4">
                @forelse ($keluhans as $k)
                    @php
                        [$badgeClass, $badgeLabel, $iconTint, $iconName] = match ($k->status) {
                            'pending' => [
                                'bg-terracotta-100 text-terracotta-600',
                                'Menunggu Proses',
                                'bg-terracotta-100 text-terracotta-600',
                                'wrench',
                            ],
                            'process' => [
                                'bg-amber-100 text-amber-600',
                                'Sedang Dikerjakan',
                                'bg-amber-100 text-amber-600',
                                'wrench',
                            ],
                            default => ['bg-sage-100 text-sage-700', 'Selesai', 'bg-sage-100 text-sage-700', 'check'],
                        };
                        $kamar = $k->user->penghunian->first()?->kamar?->nomor_kamar ?? null;
                    @endphp
                    <div class="bg-white rounded-xl border border-cream-200 shadow-sm p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0">
                                <div
                                    class="w-9 h-9 rounded-lg {{ $iconTint }} flex items-center justify-center shrink-0">
                                    @include('partial.icons.' . $iconName, ['class' => 'w-4 h-4'])
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-cream-900">{{ $k->judul }}</p>
                                    <p class="text-[11px] text-cream-500 mt-0.5 flex items-center gap-1">
                                        <span class="inline-block w-1 h-3 bg-cream-300 rounded-full"></span>
                                        {{ $kamar ? 'Kamar ' . $kamar : 'Umum' }} &bull;
                                        {{ $k->created_at->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <span
                                class="shrink-0 {{ $badgeClass }} text-[10px] font-semibold px-2.5 py-1 rounded-full">{{ $badgeLabel }}</span>
                        </div>

                        <div class="mt-3 pl-12">
                            <p class="text-xs text-cream-600 leading-relaxed">{{ $k->deskripsi }}</p>

                            @if ($k->image)
                                <img src="{{ asset('storage/' . $k->image) }}" alt="Lampiran keluhan"
                                    class="mt-3 rounded-lg border border-cream-200 max-h-48 object-cover cursor-pointer"
                                    onclick="window.open('{{ asset('storage/' . $k->image) }}', '_blank')">
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl border border-cream-200 px-4 py-8 text-center shadow-sm">
                        <p class="text-sm text-cream-600">Belum ada keluhan. Semua baik-baik saja!</p>
                    </div>
                @endforelse
            </div>

            @if ($keluhans->hasPages())
                <div class="mt-5">
                    {{ $keluhans->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="modal-create-keluhan" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()">
        </div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-cream-100">
                <h3 class="text-base font-bold text-cream-900">Buat Keluhan Baru</h3>
                <button onclick="closeCreateModal()"
                    class="text-cream-400 hover:text-cream-900 transition-colors duration-150 p-1 rounded-full hover:bg-cream-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-create-keluhan" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-[13px] font-bold text-cream-800 mb-1.5">Judul Keluhan</label>
                    <input type="text" name="judul" required placeholder="Contoh: AC Kamar Bocor"
                        class="w-full text-[14px] px-4 py-2.5 rounded-xl border border-cream-300 bg-white placeholder:text-cream-400 focus:outline-none focus:ring-4 focus:ring-sage-500/10 focus:border-sage-500 transition-all duration-200">
                </div>

                <div>
                    <label class="block text-[13px] font-bold text-cream-800 mb-1.5">Deskripsi Detail</label>
                    <textarea name="deskripsi" rows="4" required placeholder="Jelaskan detail kendala yang Anda alami..."
                        class="w-full text-[14px] px-4 py-2.5 rounded-xl border border-cream-300 bg-white placeholder:text-cream-400 focus:outline-none focus:ring-4 focus:ring-sage-500/10 focus:border-sage-500 transition-all duration-200 resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-[13px] font-bold text-cream-800 mb-1.5">
                        Lampirkan Foto <span class="text-cream-400 font-medium font-normal">(Opsional)</span>
                    </label>

                    <label for="keluhan-image-input" id="drop-zone"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-cream-300 rounded-xl bg-cream-50 hover:bg-cream-100 hover:border-sage-400 transition-colors cursor-pointer relative overflow-hidden group">

                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4"
                            id="upload-placeholder">
                            <svg class="w-7 h-7 mb-2 text-cream-500 group-hover:text-sage-500 transition-colors"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0Z" />
                            </svg>
                            <p
                                class="text-[13px] font-semibold text-cream-700 group-hover:text-sage-700 transition-colors">
                                Klik untuk unggah foto</p>
                            <p class="text-[11px] text-cream-500 mt-0.5">Format JPG, PNG, atau WebP maks 2MB.</p>
                        </div>

                        <input type="file" name="image" id="keluhan-image-input" accept=".jpg,.jpeg,.png,.webp"
                            class="hidden" onchange="previewFileName(this)">

                        <div id="file-preview-container"
                            class="hidden absolute inset-0 bg-sage-50 flex-col items-center justify-center p-4 text-center w-full">
                            <div
                                class="w-10 h-10 rounded-full bg-sage-200 text-sage-700 flex items-center justify-center mb-2 shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </div>
                            <p id="file-name-display" class="text-[13px] font-bold text-sage-800 truncate w-full px-4">
                            </p>
                            <p class="text-[11px] text-sage-600 mt-1 cursor-pointer hover:underline"
                                onclick="event.preventDefault(); document.getElementById('keluhan-image-input').click()">
                                Ganti foto</p>
                        </div>
                    </label>
                </div>

                <p id="create-keluhan-error"
                    class="hidden text-xs font-medium text-terracotta-600 bg-terracotta-50 p-2 rounded-lg"></p>
            </form>

            <div
                class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-100 bg-cream-50/50 rounded-b-2xl">
                <button onclick="closeCreateModal()"
                    class="text-[14px] font-semibold text-cream-600 hover:text-cream-900 px-4 py-2 transition-colors duration-150">Batal</button>
                <button onclick="submitCreateKeluhan()"
                    class="bg-sage-700 text-white text-[14px] font-semibold px-5 py-2.5 rounded-full hover:bg-sage-800 shadow-md shadow-sage-700/20 active:scale-[0.98] transition-all duration-200 ease-out">Kirim
                    Keluhan</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openCreateModal() {
            document.getElementById('modal-create-keluhan').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('modal-create-keluhan').classList.add('hidden');
            document.getElementById('form-create-keluhan').reset();
            document.getElementById('create-keluhan-error').classList.add('hidden');

            const placeholder = document.getElementById('upload-placeholder');
            const previewContainer = document.getElementById('file-preview-container');
            if (placeholder && previewContainer) {
                placeholder.classList.remove('hidden');
                previewContainer.classList.add('hidden');
                previewContainer.classList.remove('flex');
            }
        }

        async function submitCreateKeluhan() {
            const form = document.getElementById('form-create-keluhan');
            const errorEl = document.getElementById('create-keluhan-error');
            errorEl.classList.add('hidden');

            const formData = new FormData(form);

            if (!formData.get('judul') || !formData.get('deskripsi')) {
                errorEl.textContent = 'Judul dan deskripsi wajib diisi.';
                errorEl.classList.remove('hidden');
                return;
            }

            try {
                const res = await fetch('{{ route('penghuni.keluhan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                });
                const json = await res.json();

                if (!res.ok) {
                    errorEl.textContent = json.message || 'Gagal membuat keluhan.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                window.location.reload();
            } catch (err) {
                errorEl.textContent = 'Terjadi kesalahan jaringan.';
                errorEl.classList.remove('hidden');
            }
        }

        function previewFileName(input) {
            const placeholder = document.getElementById('upload-placeholder');
            const previewContainer = document.getElementById('file-preview-container');
            const nameDisplay = document.getElementById('file-name-display');

            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                nameDisplay.textContent = fileName;

                placeholder.classList.add('hidden');
                previewContainer.classList.remove('hidden');
                previewContainer.classList.add('flex');
            } else {
                placeholder.classList.remove('hidden');
                previewContainer.classList.add('hidden');
                previewContainer.classList.remove('flex');
            }
        }
    </script>
@endpush
