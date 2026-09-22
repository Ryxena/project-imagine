@extends('layout.admin.AdminDashboardLayout')

@section('page-title', 'Kamar')
@section('page-subtitle', 'Kelola ketersediaan dan detail kamar kost Anda.')

@section('content')
    @php
        $totalKamar = $kamar->count();
        $terisiCount = $kamar->whereNotNull('penghuni_kamar_id')->count();
        $kosongCount = $totalKamar - $terisiCount;
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 animate-in my-2">
        <div class="flex flex-wrap gap-2">
            <button data-filter="semua"
                class="filter-tab bg-sage-700 text-white text-xs font-semibold px-4 py-2 rounded-full transition-all duration-150 ease-out shadow-2xs">
                Semua ({{ $totalKamar }})
            </button>
            <button data-filter="terisi"
                class="filter-tab bg-white border border-cream-300 text-cream-600 text-xs font-semibold px-4 py-2 rounded-full hover:bg-cream-50 transition-all duration-150 ease-out shadow-2xs">
                Terisi ({{ $terisiCount }})
            </button>
            <button data-filter="kosong"
                class="filter-tab bg-white border border-cream-300 text-cream-600 text-xs font-semibold px-4 py-2 rounded-full hover:bg-cream-50 transition-all duration-150 ease-out shadow-2xs">
                Kosong ({{ $kosongCount }})
            </button>
        </div>

        <button onclick="openModalTambah()"
            class="flex items-center justify-center gap-2 bg-sage-700 text-white text-xs font-semibold px-4.5 py-2.5 rounded-xl hover:bg-sage-800 hover:shadow-md active:scale-[0.98] transition-all duration-150 ease-out shadow-sm shrink-0">
            + Tambah Kamar
        </button>
    </div>

    <div id="kamar-grid" class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 animate-in"
        style="animation-delay: 0.1s">
        @forelse ($kamar as $k)
            @php
                $isTerisi = !is_null($k->penghuni_kamar_id);

                $statusBorderClass = $isTerisi
                    ? 'bg-sage-50/60 border-sage-200'
                    : 'bg-white border-cream-200 border-l-4 border-l-sage-500';

                $tipeBadgeClass = match (strtolower($k->tipe_kamar)) {
                    'vip' => 'bg-amber-100 text-amber-800 border border-amber-200',
                    'deluxe' => 'bg-sage-100 text-sage-800 border border-sage-200',
                    default => 'bg-cream-100 text-cream-700 border border-cream-200',
                };
            @endphp

            <div data-status="{{ $isTerisi ? 'terisi' : 'kosong' }}"
                class="kamar-card group relative rounded-2xl p-5 shadow-sm border {{ $statusBorderClass }} hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-out flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div
                                class="w-11 h-11 rounded-xl {{ $isTerisi ? 'bg-sage-100 text-sage-700' : 'bg-cream-100 text-cream-700' }} flex items-center justify-center shrink-0 shadow-2xs">
                                @include('partial.icons.door', ['class' => 'w-5 h-5'])
                            </div>
                            <div class="min-w-0">
                                <p class="text-base font-bold text-cream-900 truncate">Kamar {{ $k->nomor_kamar }}</p>
                                <span
                                    class="inline-block mt-0.5 {{ $tipeBadgeClass }} text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                    {{ $k->tipe_kamar }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1 shrink-0">
                            <button onclick="openModalEdit({{ json_data_kamar($k) }})" title="Edit Kamar"
                                class="w-8 h-8 rounded-lg text-cream-400 hover:text-sage-700 hover:bg-sage-100/60 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>

                            @if ($isTerisi)
                                <button type="button" disabled
                                    title="Kamar tidak dapat dihapus karena sedang terisi oleh penghuni"
                                    class="w-8 h-8 rounded-lg text-cream-300 bg-cream-50 opacity-40 cursor-not-allowed flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            @else
                                <button onclick="openModalHapus('{{ $k->id }}', '{{ $k->nomor_kamar }}')"
                                    title="Hapus Kamar"
                                    class="w-8 h-8 rounded-lg text-cream-400 hover:text-terracotta-600 hover:bg-terracotta-50 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

               
                    @if ($k->deskripsi)
                        <p class="mt-3 text-xs text-cream-600 line-clamp-2 leading-relaxed">{{ $k->deskripsi }}</p>
                    @endif
                 
                    <p class="mt-4 text-xl font-extrabold text-cream-900">
                        Rp{{ number_format($k->harga, 0, ',', '.') }}
                        <span class="text-xs font-medium text-cream-500">/ bulan</span>
                    </p>
                </div>

                <div class="mt-5 pt-3.5 border-t border-cream-200/60 flex items-center justify-between">
                    @if ($isTerisi)
                        <div class="flex items-center gap-2.5 min-w-0">
                            @if (!empty($k->image))
                                <img src="{{ asset('storage/' . $k->image) }}" alt="Foto {{ $k->name }}"
                                    class="w-7 h-7 rounded-full object-cover shrink-0 border border-sage-300 shadow-2xs">
                            @else
                                <div
                                    class="w-7 h-7 rounded-full bg-sage-200 text-sage-800 text-xs font-bold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($k->name ?? '?', 0, 1)) }}
                                </div>
                            @endif
                            <p class="text-xs font-bold text-cream-900 truncate">{{ $k->name }}</p>
                        </div>
                        <span
                            class="shrink-0 bg-sage-200/70 text-sage-800 text-[10px] font-extrabold px-3 py-1 rounded-full">Terisi</span>
                    @else
                        <p class="text-xs font-bold text-sage-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-sage-500 animate-pulse"></span>
                            Siap Dihuni
                        </p>
                        <span
                            class="shrink-0 bg-sage-600 text-white text-[10px] font-extrabold px-3 py-1 rounded-full shadow-2xs">Kosong</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-cream-200 px-6 py-12 text-center shadow-sm">
                <div
                    class="w-12 h-12 rounded-2xl bg-cream-100 text-cream-500 flex items-center justify-center mx-auto mb-3">
                    @include('partial.icons.door', ['class' => 'w-6 h-6'])
                </div>
                <p class="text-sm font-bold text-cream-800">Belum ada data kamar.</p>
                <p class="text-xs text-cream-500 mt-1">Silakan tambahkan kamar baru melalui tombol di atas.</p>
            </div>
        @endforelse

        <div id="filter-empty-state"
            class="hidden col-span-full bg-white rounded-2xl border border-cream-200 px-6 py-12 text-center shadow-sm animate-in">
            <div class="w-12 h-12 rounded-2xl bg-cream-100 text-cream-500 flex items-center justify-center mx-auto mb-3">
                @include('partial.icons.door', ['class' => 'w-6 h-6'])
            </div>
            <p class="text-sm font-bold text-cream-800">Tidak ada kamar pada kategori ini.</p>
            <p class="text-xs text-cream-500 mt-1">Coba pilih filter status lainnya untuk melihat data kamar.</p>
        </div>
    </div>

    <div id="modal-kamar" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40 backdrop-blur-2xs transition-opacity" onclick="closeModalKamar()">
        </div>
        <div class="relative bg-white rounded-3xl shadow-xl w-full max-w-md overflow-hidden animate-in">
            <div class="flex items-center justify-between px-6 py-5 border-b border-cream-200">
                <h3 id="modal-title" class="text-base font-bold text-cream-900">Tambah Kamar Baru</h3>
                <button onclick="closeModalKamar()"
                    class="w-8 h-8 rounded-xl text-cream-400 hover:text-cream-700 hover:bg-cream-100 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-kamar" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" id="kamar-id" name="id">
                <input type="hidden" id="form-method" value="POST">

                <div>
                    <label class="block text-xs font-bold text-cream-700 mb-1.5 uppercase tracking-wider">Nomor / Nama
                        Kamar</label>
                    <input type="text" id="nomor-kamar" name="nomor_kamar" required
                        placeholder="e.g., 101, Kamar Anggrek"
                        class="w-full text-sm px-4 py-3 rounded-xl border border-cream-300 bg-cream-50/50 focus:outline-none focus:ring-2 focus:ring-sage-400 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-cream-700 mb-1.5 uppercase tracking-wider">Tipe
                        Kamar</label>
                    <select id="tipe-kamar" name="tipe_kamar" required
                        class="w-full text-sm px-4 py-3 rounded-xl border border-cream-300 bg-cream-50/50 focus:outline-none focus:ring-2 focus:ring-sage-400 transition-all">
                        <option value="">Pilih tipe kamar</option>
                        <option value="standar">Standar</option>
                        <option value="deluxe">Deluxe</option>
                        <option value="vip">VIP</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-cream-700 mb-1.5 uppercase tracking-wider">Harga Sewa per
                        Bulan</label>
                    <div
                        class="flex items-center rounded-xl border border-cream-300 bg-cream-50/50 overflow-hidden focus-within:ring-2 focus-within:ring-sage-400 transition-all">
                        <span class="pl-4 text-sm font-semibold text-cream-500">Rp</span>
                        <input type="number" id="harga-kamar" name="harga" required min="0"
                            placeholder="1500000" class="w-full text-sm px-2.5 py-3 bg-transparent focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-cream-700 mb-1.5 uppercase tracking-wider">Deskripsi Singkat
                        (Fasilitas dll)</label>
                    <textarea id="deskripsi-kamar" name="deskripsi" rows="3"
                        placeholder="Fasilitas kamar: AC, WiFi, Kamar Mandi Dalam..."
                        class="w-full text-sm px-4 py-3 rounded-xl border border-cream-300 bg-cream-50/50 focus:outline-none focus:ring-2 focus:ring-sage-400 transition-all resize-none"></textarea>
                </div>

                <p id="form-error"
                    class="hidden text-xs font-semibold text-terracotta-600 bg-terracotta-50 border border-terracotta-200 p-3 rounded-xl">
                </p>
            </form>

            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-cream-50/50 border-t border-cream-200">
                <button onclick="closeModalKamar()"
                    class="text-xs font-semibold text-cream-600 hover:text-cream-900 px-4 py-2.5 rounded-xl transition-colors">
                    Batal
                </button>
                <button onclick="submitFormKamar()" id="btn-submit-kamar"
                    class="bg-sage-700 text-white text-xs font-semibold px-5 py-2.5 rounded-xl hover:bg-sage-800 active:scale-[0.98] transition-all shadow-sm">
                    Simpan Kamar
                </button>
            </div>
        </div>
    </div>

    <div id="modal-hapus" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40 backdrop-blur-2xs" onclick="closeModalHapus()"></div>
        <div class="relative bg-white rounded-3xl shadow-xl w-full max-w-sm overflow-hidden p-6 text-center animate-in">
            <div
                class="w-12 h-12 rounded-2xl bg-terracotta-100 text-terracotta-600 flex items-center justify-center mx-auto mb-4">
                @include('partial.icons.warning', ['class' => 'w-6 h-6'])
            </div>
            <h3 class="text-base font-bold text-cream-900">Hapus Kamar Ini?</h3>
            <p id="hapus-message" class="text-xs text-cream-600 mt-1.5 leading-relaxed">
                Tindakan ini tidak dapat dibatalkan. Pastikan kamar tidak sedang dihuni.
            </p>
            <input type="hidden" id="hapus-kamar-id">

            <div class="mt-6 flex items-center justify-center gap-3">
                <button onclick="closeModalHapus()"
                    class="w-full text-xs font-semibold text-cream-700 bg-cream-100 hover:bg-cream-200 py-3 rounded-xl transition-colors">
                    Batal
                </button>
                <button onclick="executeHapusKamar()"
                    class="w-full text-xs font-semibold text-white bg-terracotta-600 hover:bg-terracotta-700 py-3 rounded-xl transition-colors shadow-sm">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @php
        function json_data_kamar($k)
        {
            return json_encode([
                'id' => $k->id,
                'nomor_kamar' => $k->nomor_kamar,
                'tipe_kamar' => $k->tipe_kamar,
                'harga' => $k->harga,
                'deskripsi' => $k->deskripsi,
            ]);
        }
    @endphp

    <script>
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const filter = tab.dataset.filter;
                let visibleCount = 0;

                document.querySelectorAll('.filter-tab').forEach(t => {
                    t.classList.remove('bg-sage-700', 'text-white', 'shadow-2xs');
                    t.classList.add('bg-white', 'border', 'border-cream-300', 'text-cream-600');
                });
                tab.classList.add('bg-sage-700', 'text-white', 'shadow-2xs');
                tab.classList.remove('bg-white', 'border', 'border-cream-300', 'text-cream-600');

                document.querySelectorAll('.kamar-card').forEach(card => {
                    const status = card.dataset.status;
                    const isVisible = (filter === 'semua' || status === filter);

                    card.style.display = isVisible ? '' : 'none';
                    if (isVisible) {
                        visibleCount++;
                    }
                });

                const filterEmptyState = document.getElementById('filter-empty-state');
                if (filterEmptyState) {
                    filterEmptyState.classList.toggle('hidden', visibleCount > 0);
                }
            });
        });

        function openModalTambah() {
            document.getElementById('modal-title').textContent = 'Tambah Kamar Baru';
            document.getElementById('btn-submit-kamar').textContent = 'Tambah Kamar';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('kamar-id').value = '';
            document.getElementById('form-kamar').reset();
            document.getElementById('form-error').classList.add('hidden');
            document.getElementById('modal-kamar').classList.remove('hidden');
        }

        function openModalEdit(kamar) {
            document.getElementById('modal-title').textContent = 'Edit Kamar ' + kamar.nomor_kamar;
            document.getElementById('btn-submit-kamar').textContent = 'Simpan Perubahan';
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('kamar-id').value = kamar.id;

            document.getElementById('nomor-kamar').value = kamar.nomor_kamar;
            document.getElementById('tipe-kamar').value = kamar.tipe_kamar;
            document.getElementById('harga-kamar').value = kamar.harga;
            document.getElementById('deskripsi-kamar').value = kamar.deskripsi || '';

            document.getElementById('form-error').classList.add('hidden');
            document.getElementById('modal-kamar').classList.remove('hidden');
        }

        function closeModalKamar() {
            document.getElementById('modal-kamar').classList.add('hidden');
        }

        async function submitFormKamar() {
            const method = document.getElementById('form-method').value;
            const kamarId = document.getElementById('kamar-id').value;
            const errorEl = document.getElementById('form-error');
            errorEl.classList.add('hidden');

            const payload = {
                nomor_kamar: document.getElementById('nomor-kamar').value,
                tipe_kamar: document.getElementById('tipe-kamar').value,
                harga: document.getElementById('harga-kamar').value,
                deskripsi: document.getElementById('deskripsi-kamar').value,
            };

            if (!payload.nomor_kamar || !payload.tipe_kamar || !payload.harga) {
                errorEl.textContent = 'Nomor kamar, tipe kamar, dan harga wajib diisi.';
                errorEl.classList.remove('hidden');
                return;
            }

            let url = '{{ route('admin.kamar.store') }}';
            if (method === 'PUT') {
                url = `/admin/kamar/${kamarId}`;
            }

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    errorEl.textContent = json.message || 'Gagal menyimpan data kamar.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                window.location.reload();
            } catch (err) {
                errorEl.textContent = 'Terjadi kesalahan jaringan.';
                errorEl.classList.remove('hidden');
            }
        }

        function openModalHapus(id, nomor) {
            document.getElementById('hapus-kamar-id').value = id;
            document.getElementById('hapus-message').innerHTML =
                `Apakah Anda yakin ingin menghapus data <strong>Kamar ${nomor}</strong>? Tindakan ini tidak dapat dibatalkan.`;
            document.getElementById('modal-hapus').classList.remove('hidden');
        }

        function closeModalHapus() {
            document.getElementById('modal-hapus').classList.add('hidden');
        }

        async function executeHapusKamar() {
            const id = document.getElementById('hapus-kamar-id').value;
            try {
                const res = await fetch(`/admin/kamar/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    alert(json.message || 'Gagal menghapus kamar.');
                    return;
                }

                window.location.reload();
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            }
        }
    </script>
@endpush
