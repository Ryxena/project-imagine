@extends('layout.admin.AdminDashboardLayout')

@section('page-title', 'Kamar')
@section('page-subtitle', 'Kelola ketersediaan dan detail kamar kost Anda.')

@section('content')
    @php
        $totalKamar = $kamar->count();
        $terisiCount = $kamar->whereNotNull('penghuni_kamar_id')->count();
        $kosongCount = $totalKamar - $terisiCount;
    @endphp

    <div class="flex items-center justify-between animate-in my-2">
        <div class="flex gap-2">
            <button data-filter="semua"
                class="filter-tab bg-sage-700 text-white text-xs font-medium px-3.5 py-1.5 rounded-full transition-all duration-150 ease-out">
                Semua ({{ $totalKamar }})
            </button>
            <button data-filter="terisi"
                class="filter-tab bg-cream-200 text-cream-600 text-xs font-medium px-3.5 py-1.5 rounded-full hover:bg-cream-200 transition-all duration-150 ease-out">
                Terisi ({{ $terisiCount }})
            </button>
            <button data-filter="kosong"
                class="filter-tab bg-cream-200 text-cream-600 text-xs font-medium px-3.5 py-1.5 rounded-full hover:bg-cream-200 transition-all duration-150 ease-out">
                Kosong ({{ $kosongCount }})
            </button>
        </div>

        <button onclick="openModal()"
            class="flex items-center gap-2 bg-sage-700 text-white text-xs font-medium px-4 py-2 rounded-md hover:bg-sage-800 hover:shadow-md active:scale-[0.98] transition-all duration-150 ease-out">
            + Tambah Kamar
        </button>
    </div>

    <div id="kamar-grid" class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 animate-in"
        style="animation-delay: 0.1s">
        @forelse ($kamar as $k)
            @php
                $isTerisi = !is_null($k->penghuni_kamar_id);
            @endphp

            <div data-status="{{ $isTerisi ? 'terisi' : 'kosong' }}"
                class="kamar-card bg-white rounded-xl p-4 shadow-sm border border-cream-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-out">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div
                            class="w-10 h-10 rounded-lg bg-cream-100 text-cream-600 flex items-center justify-center shrink-0">
                            @include('partial.icons.door', ['class' => 'w-4 h-4'])
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-cream-900">Kamar {{ $k->nomor_kamar }}</p>
                            <span
                                class="inline-block mt-1 bg-sage-100 text-sage-700 text-[10px] font-semibold px-2 py-0.5 rounded-full">{{ $k->tipe_kamar }}</span>
                        </div>
                    </div>
                </div>

                @if ($k->deskripsi)
                    <p class="mt-2 text-[11px] text-cream-600 line-clamp-2">{{ $k->deskripsi }}</p>
                @endif

                <p class="mt-3 text-lg font-bold text-cream-900">
                    Rp{{ number_format($k->harga, 0, ',', '.') }} <span class="text-xs font-normal text-cream-600">/
                        bulan</span>
                </p>

                <div class="mt-3 pt-3 border-t border-cream-100 flex items-center justify-between">
                    @if ($isTerisi)
                        <div class="flex items-center gap-2 min-w-0">
                            @if (!empty($k->image))
                                <img src="{{ asset('storage/' . $k->image) }}" alt="Foto {{ $k->name }}"
                                    class="w-6 h-6 rounded-full object-cover shrink-0 border border-sage-300 shadow-sm">
                            @else
                                <div
                                    class="w-6 h-6 rounded-full bg-sage-200 text-sage-800 text-[10px] font-semibold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($k->name ?? '?', 0, 1)) }}
                                </div>
                            @endif
                            <p class="text-xs text-cream-900 truncate">{{ $k->name }}</p>
                        </div>
                        <span
                            class="shrink-0 bg-sage-100 text-sage-700 text-[10px] font-semibold px-2.5 py-1 rounded-full">Terisi</span>
                    @else
                        <p class="text-xs text-cream-600">Tidak ada penghuni</p>
                        <span
                            class="shrink-0 bg-cream-100 text-cream-600 text-[10px] font-semibold px-2.5 py-1 rounded-full">Kosong</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-cream-200 px-4 py-8 text-center shadow-sm">
                <p class="text-sm text-cream-600">Belum ada data kamar.</p>
            </div>
        @endforelse
    </div>

    <div id="modal-tambah-kamar" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-md animate-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200">
                <h3 class="text-base font-semibold text-cream-900">Tambah Kamar Baru</h3>
                <button onclick="closeModal()" class="text-cream-400 hover:text-cream-700 transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-tambah-kamar" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Nama / Nomor Kamar</label>
                    <input type="text" name="nomor_kamar" required placeholder="e.g., 101, Kamar Anggrek"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>

                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Tipe Kamar</label>
                    <select name="tipe_kamar" required
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                        <option value="">Pilih tipe kamar</option>
                        <option value="standar">Standar</option>
                        <option value="deluxe">Deluxe</option>
                        <option value="vip">VIP</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Harga Sewa per Bulan</label>
                    <div
                        class="flex items-center rounded-lg border border-cream-300 bg-cream-50 focus-within:ring-2 focus-within:ring-sage-400 focus-within:border-sage-400 transition-colors duration-150">
                        <span class="pl-3.5 text-sm text-cream-500">Rp</span>
                        <input type="number" name="harga" required min="0" placeholder="1500000"
                            class="w-full text-sm px-2 py-2.5 bg-transparent focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Deskripsi Singkat (Fasilitas dll)</label>
                    <textarea name="deskripsi" rows="3" placeholder="Fasilitas kamar: AC, WiFi, Kamar Mandi Dalam..."
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150 resize-none"></textarea>
                </div>

                <p id="form-error" class="hidden text-xs text-terracotta-600"></p>
            </form>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
                <button onclick="closeModal()"
                    class="text-sm font-medium text-cream-600 hover:text-cream-900 transition-colors duration-150">
                    Batal
                </button>
                <button onclick="submitTambahKamar()"
                    class="bg-sage-700 text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-sage-800 active:scale-[0.98] transition-all duration-150 ease-out">
                    Tambah Kamar
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const filter = tab.dataset.filter;

                document.querySelectorAll('.filter-tab').forEach(t => {
                    t.classList.remove('bg-sage-700', 'text-white');
                    t.classList.add('bg-cream-100', 'text-cream-600');
                });
                tab.classList.add('bg-sage-700', 'text-white');
                tab.classList.remove('bg-cream-100', 'text-cream-600');

                document.querySelectorAll('.kamar-card').forEach(card => {
                    const status = card.dataset.status;
                    card.style.display = (filter === 'semua' || status === filter) ? '' : 'none';
                });
            });
        });

        function openModal() {
            document.getElementById('modal-tambah-kamar').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal-tambah-kamar').classList.add('hidden');
            document.getElementById('form-tambah-kamar').reset();
            document.getElementById('form-error').classList.add('hidden');
        }

        async function submitTambahKamar() {
            const form = document.getElementById('form-tambah-kamar');
            const errorEl = document.getElementById('form-error');
            errorEl.classList.add('hidden');

            const formData = new FormData(form);
            const payload = {
                nomor_kamar: formData.get('nomor_kamar'),
                tipe_kamar: formData.get('tipe_kamar'),
                harga: formData.get('harga'),
                deskripsi: formData.get('deskripsi') || '',
            };

            if (!payload.nomor_kamar || !payload.tipe_kamar || !payload.harga) {
                errorEl.textContent = 'Nama kamar, tipe kamar, dan harga wajib diisi.';
                errorEl.classList.remove('hidden');
                return;
            }

            try {
                const res = await fetch('{{ route('admin.kamar.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    errorEl.textContent = json.message || 'Gagal menambahkan kamar.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                window.location.reload();
            } catch (err) {
                errorEl.textContent = 'Terjadi kesalahan jaringan.';
                errorEl.classList.remove('hidden');
            }
        }
    </script>
@endpush
