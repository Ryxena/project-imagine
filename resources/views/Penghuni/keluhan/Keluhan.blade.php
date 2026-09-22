@extends('layout.penghuni.PenghuniDashboardLayout')

@section('page-title', 'Keluhan Saya')
@section('page-subtitle', 'Sampaikan kendala yang kamu alami.')

@section('content')
    @include('partial.tenant-status-banner')

    @php
        $isCanCreate = !in_array($tenantStatus, ['unassigned', 'no_record', 'checked_out']);
    @endphp

    @if ($isCanCreate)
        <button onclick="openCreateModal()"
            class="flex items-center gap-2 bg-sage-700 text-white text-xs font-medium px-4 py-2.5 rounded-full hover:bg-sage-800 hover:shadow-md active:scale-[0.98] transition-all duration-150 ease-out animate-in">
            + Buat Keluhan Baru
        </button>
    @else
        <button disabled
            class="flex items-center gap-2 bg-cream-200/60 text-cream-600 text-xs font-medium px-4 py-2.5 rounded-full cursor-not-allowed border border-cream-200/50 opacity-60 animate-in">
            + Buat Keluhan Baru
        </button>
    @endif

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

                    <div class="bg-white rounded-xl border border-cream-200 shadow-sm p-4 sm:p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">

                            <div class="flex items-start gap-3 min-w-0">
                                <div
                                    class="w-9 h-9 rounded-lg {{ $iconTint }} flex items-center justify-center shrink-0 mt-0.5">
                                    @include('partial.icons.' . $iconName, ['class' => 'w-4 h-4'])
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-cream-900 leading-snug">{{ $k->judul }}</p>
                                    <p class="text-[11px] text-cream-500 mt-0.5 flex items-center gap-1">
                                        <span class="inline-block w-1 h-3 bg-cream-300 rounded-full shrink-0"></span>
                                        {{ $kamar ? 'Kamar ' . $kamar : 'Umum' }} &bull;
                                        {{ $k->created_at->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between sm:justify-end gap-2 shrink-0 pt-2 sm:pt-0 border-t sm:border-0 border-cream-100">
                                <span
                                    class="{{ $badgeClass }} text-[10px] font-semibold px-2.5 py-1 rounded-full">{{ $badgeLabel }}</span>

                                @if ($k->status === 'pending' && $isCanCreate)
                                    <div class="flex items-center gap-1 border-l border-cream-200 pl-2 ml-1">
                                        <button onclick="openEditModal({{ $k->id }})" title="Edit Keluhan"
                                            class="w-7 h-7 rounded-lg text-cream-400 hover:text-sage-700 hover:bg-sage-50 flex items-center justify-center transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                        <button
                                            onclick="openDeleteModal({{ $k->id }}, '{{ addslashes($k->judul) }}')"
                                            title="Hapus Keluhan"
                                            class="w-7 h-7 rounded-lg text-cream-400 hover:text-terracotta-600 hover:bg-terracotta-50 flex items-center justify-center transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 pl-0 sm:pl-12">
                            <p class="text-xs text-cream-600 leading-relaxed">{{ $k->deskripsi }}</p>

                            @if ($k->image)
                                <img src="{{ asset('storage/' . $k->image) }}" alt="Lampiran keluhan"
                                    class="mt-3 rounded-lg border border-cream-200 max-h-48 w-full sm:w-auto object-cover cursor-pointer shadow-2xs"
                                    onclick="window.open('{{ asset('storage/' . $k->image) }}', '_blank')">
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl border border-cream-200 px-6 py-10 text-center shadow-sm space-y-3">
                        @if (in_array($tenantStatus, ['unassigned', 'no_record']))
                            <div
                                class="w-12 h-12 rounded-full bg-cream-100 text-cream-600 flex items-center justify-center mx-auto mb-1">
                                @include('partial.icons.door', ['class' => 'w-6 h-6'])
                            </div>
                            <p class="text-sm font-bold text-cream-900">Belum Ada Riwayat Keluhan</p>
                            <p class="text-xs text-cream-600 max-w-sm mx-auto leading-relaxed">
                                Kamu bisa mulai menyampaikan kendala fasilitas setelah resmi mendapatkan penempatan kamar
                                dari pengelola.
                            </p>
                        @else
                            <div
                                class="w-12 h-12 rounded-full bg-sage-100 text-sage-700 flex items-center justify-center mx-auto mb-1">
                                @include('partial.icons.check', ['class' => 'w-6 h-6'])
                            </div>
                            <p class="text-sm font-bold text-cream-900">Semua Fasilitas Aman & Terkendali!</p>
                            <p class="text-xs text-cream-600 max-w-sm mx-auto leading-relaxed">
                                Ada kendala dengan fasilitas kamar atau lingkungan kos? Jangan ragu untuk sampaikan ke kami
                                lewat tombol di atas ya.
                            </p>
                        @endif
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

    <div id="modal-form-keluhan" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40 backdrop-blur-sm transition-opacity" onclick="closeFormModal()">
        </div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-cream-100">
                <h3 id="modal-title-keluhan" class="text-base font-bold text-cream-900">Buat Keluhan Baru</h3>
                <button onclick="closeFormModal()"
                    class="text-cream-400 hover:text-cream-900 transition-colors duration-150 p-1 rounded-full hover:bg-cream-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-keluhan" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="id" id="keluhan-id">
                <input type="hidden" name="_method" id="keluhan-method" value="POST">

                <div>
                    <label class="block text-[13px] font-bold text-cream-800 mb-1.5">Judul Keluhan</label>
                    <input type="text" name="judul" id="keluhan-judul" required
                        placeholder="Contoh: AC Kamar Bocor"
                        class="w-full text-[14px] px-4 py-2.5 rounded-xl border border-cream-300 bg-white placeholder:text-cream-400 focus:outline-none focus:ring-4 focus:ring-sage-500/10 focus:border-sage-500 transition-all duration-200">
                </div>

                <div>
                    <label class="block text-[13px] font-bold text-cream-800 mb-1.5">Deskripsi Detail</label>
                    <textarea name="deskripsi" id="keluhan-deskripsi" rows="4" required
                        placeholder="Jelaskan detail kendala yang Anda alami..."
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

                <p id="form-keluhan-error"
                    class="hidden text-xs font-medium text-terracotta-600 bg-terracotta-50 p-2 rounded-lg"></p>
            </form>

            <div
                class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-100 bg-cream-50/50 rounded-b-2xl">
                <button onclick="closeFormModal()"
                    class="text-[14px] font-semibold text-cream-600 hover:text-cream-900 px-4 py-2 transition-colors duration-150">Batal</button>
                <button onclick="submitFormKeluhan()" id="btn-submit-keluhan"
                    class="bg-sage-700 text-white text-[14px] font-semibold px-5 py-2.5 rounded-full hover:bg-sage-800 shadow-md shadow-sage-700/20 active:scale-[0.98] transition-all duration-200 ease-out">Kirim
                    Keluhan</button>
            </div>
        </div>
    </div>

    <div id="modal-delete-keluhan" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()">
        </div>
        <div
            class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center animate-in fade-in zoom-in-95 duration-200">
            <div
                class="w-12 h-12 rounded-full bg-terracotta-100 text-terracotta-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-cream-900">Batalkan Keluhan?</h3>
            <p id="delete-keluhan-message" class="text-xs text-cream-600 mt-2 leading-relaxed"></p>
            <input type="hidden" id="delete-keluhan-id">
            <div class="mt-6 flex items-center gap-3">
                <button onclick="closeDeleteModal()"
                    class="flex-1 text-[13px] font-semibold text-cream-700 bg-cream-100 hover:bg-cream-200 py-2.5 rounded-xl transition-colors">Tidak,
                    Kembali</button>
                <button onclick="executeDeleteKeluhan()"
                    class="flex-1 text-[13px] font-semibold text-white bg-terracotta-600 hover:bg-terracotta-700 py-2.5 rounded-xl transition-colors">Ya,
                    Batalkan</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const keluhanData = @json($keluhans->keyBy('id'));

        function openCreateModal() {
            document.getElementById('modal-title-keluhan').textContent = 'Buat Keluhan Baru';
            document.getElementById('btn-submit-keluhan').textContent = 'Kirim Keluhan';
            document.getElementById('keluhan-id').value = '';
            document.getElementById('keluhan-method').value = 'POST';

            document.getElementById('form-keluhan').reset();
            document.getElementById('form-keluhan-error').classList.add('hidden');

            resetImagePreview();
            document.getElementById('modal-form-keluhan').classList.remove('hidden');
        }

        function openEditModal(id) {
            const k = keluhanData[id];

            document.getElementById('modal-title-keluhan').textContent = 'Edit Keluhan';
            document.getElementById('btn-submit-keluhan').textContent = 'Simpan Perubahan';
            document.getElementById('keluhan-id').value = k.id;
            document.getElementById('keluhan-method').value = 'PUT';

            document.getElementById('keluhan-judul').value = k.judul;
            document.getElementById('keluhan-deskripsi').value = k.deskripsi;

            document.getElementById('form-keluhan-error').classList.add('hidden');
            resetImagePreview();

            if (k.image) {
                const placeholder = document.getElementById('upload-placeholder');
                const previewContainer = document.getElementById('file-preview-container');
                const nameDisplay = document.getElementById('file-name-display');

                nameDisplay.textContent = "(Terdapat foto tersimpan)";
                placeholder.classList.add('hidden');
                previewContainer.classList.remove('hidden');
                previewContainer.classList.add('flex');
            }

            document.getElementById('modal-form-keluhan').classList.remove('hidden');
        }

        function closeFormModal() {
            document.getElementById('modal-form-keluhan').classList.add('hidden');
        }

        function openDeleteModal(id, judul) {
            document.getElementById('delete-keluhan-id').value = id;
            document.getElementById('delete-keluhan-message').innerHTML =
                `Keluhan <strong>"${judul}"</strong> akan dihapus dan dibatalkan secara permanen.`;
            document.getElementById('modal-delete-keluhan').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('modal-delete-keluhan').classList.add('hidden');
        }

        function resetImagePreview() {
            const placeholder = document.getElementById('upload-placeholder');
            const previewContainer = document.getElementById('file-preview-container');
            if (placeholder && previewContainer) {
                placeholder.classList.remove('hidden');
                previewContainer.classList.add('hidden');
                previewContainer.classList.remove('flex');
            }
        }

        async function submitFormKeluhan() {
            const form = document.getElementById('form-keluhan');
            const errorEl = document.getElementById('form-keluhan-error');
            errorEl.classList.add('hidden');

            const formData = new FormData(form);
            const id = document.getElementById('keluhan-id').value;

            if (!formData.get('judul') || !formData.get('deskripsi')) {
                errorEl.textContent = 'Judul dan deskripsi wajib diisi.';
                errorEl.classList.remove('hidden');
                return;
            }

            let url = '{{ route('penghuni.keluhan.store') }}';
            if (id) {
                url = `/penghuni/keluhan/${id}`;
            }

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                });
                const json = await res.json();

                if (!res.ok) {
                    errorEl.textContent = json.message || 'Gagal menyimpan keluhan.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                window.location.reload();
            } catch (err) {
                errorEl.textContent = 'Terjadi kesalahan jaringan.';
                errorEl.classList.remove('hidden');
            }
        }

        async function executeDeleteKeluhan() {
            const id = document.getElementById('delete-keluhan-id').value;

            try {
                const res = await fetch(`/penghuni/keluhan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                const json = await res.json();

                if (!res.ok) {
                    alert(json.message || 'Gagal menghapus keluhan.');
                    return;
                }

                window.location.reload();
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
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
                resetImagePreview();
            }
        }
    </script>
@endpush
