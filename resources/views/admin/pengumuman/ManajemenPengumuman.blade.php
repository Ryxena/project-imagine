@extends('layout.admin.AdminDashboardLayout')

@section('page-title', 'Pengumuman')
@section('page-subtitle', 'Kelola dan sebarkan informasi penting ke seluruh penghuni kost.')

@section('content')
    <div class="flex justify-end animate-in">
        <button onclick="openCreateModal()"
            class="flex items-center gap-2 bg-sage-700 text-white text-xs font-medium px-4 py-2.5 rounded-lg hover:bg-sage-800 hover:shadow-md active:scale-[0.98] transition-all duration-150 ease-out">
            + Buat Pengumuman Baru
        </button>
    </div>

    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 animate-in" style="animation-delay: 0.1s">
        @forelse ($pengumuman as $p)
            @php
                [$badgeClass, $badgeLabel] = match ($p->type) {
                    'penting' => ['bg-terracotta-100 text-terracotta-600', 'Penting'],
                    'informasi' => ['bg-sage-100 text-sage-700', 'Informasi'],
                    default => ['bg-cream-100 text-cream-600', 'Umum'],
                };
            @endphp
            <div
                class="bg-white rounded-xl p-4 shadow-sm border border-cream-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-out flex flex-col">
                <div class="flex items-center justify-between">
                    <span
                        class="{{ $badgeClass }} text-[10px] font-semibold px-2.5 py-1 rounded-full">{{ $badgeLabel }}</span>
                    <p class="text-[11px] text-cream-500">
                        {{ \Carbon\Carbon::parse($p->tanggal_publish)->translatedFormat('d M Y') }}</p>
                </div>
                <p class="mt-3 text-sm font-semibold text-cream-900">{{ $p->judul }}</p>
                <p class="mt-1.5 text-xs text-cream-600 line-clamp-3 flex-1">{{ $p->deskripsi }}</p>
                <div class="mt-3 pt-3 border-t border-cream-100 flex justify-end">
                    <button onclick="openDetailModal({{ $p->id }})"
                        class="text-xs font-medium text-sage-700 hover:text-sage-800 transition-colors duration-150">
                        Lihat Detail
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-cream-200 px-4 py-8 text-center shadow-sm">
                <p class="text-sm text-cream-600">Belum ada pengumuman.</p>
            </div>
        @endforelse
    </div>

    @if ($pengumuman->hasPages())
        <div class="mt-6">
            {{ $pengumuman->links('vendor.pagination.tailwind') }}
        </div>
    @endif

    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeCreateModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-lg animate-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200">
                <div>
                    <h3 class="text-base font-semibold text-cream-900">Buat Pengumuman Baru</h3>
                    <p class="mt-0.5 text-xs text-cream-600">Akan langsung terbit dan dapat dibaca seluruh penghuni aktif.
                    </p>
                </div>
                <button onclick="closeCreateModal()"
                    class="text-cream-400 hover:text-cream-700 transition-colors duration-150 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-create" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Judul Pengumuman</label>
                    <input type="text" name="judul" required placeholder="Contoh: Jadwal Pemadaman Listrik"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Kategori</label>
                    <select name="type" required
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                        <option value="umum">Umum</option>
                        <option value="informasi">Informasi</option>
                        <option value="penting">Penting</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Isi Pengumuman</label>
                    <textarea name="deskripsi" rows="5" required placeholder="Tuliskan detail pengumuman di sini..."
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150 resize-none"></textarea>
                </div>
                <p id="create-error" class="hidden text-xs text-terracotta-600"></p>
            </form>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
                <button onclick="closeCreateModal()"
                    class="text-sm font-medium text-cream-600 hover:text-cream-900 transition-colors duration-150">Batal</button>
                <button onclick="submitCreate()"
                    class="bg-sage-700 text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-sage-800 active:scale-[0.98] transition-all duration-150 ease-out">Umumkan</button>
            </div>
        </div>
    </div>

    <div id="modal-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeDetailModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-lg animate-in max-h-[85vh] flex flex-col">
            <div class="flex items-start justify-between px-6 py-4 border-b border-cream-200">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span id="dd-badge" class="text-[10px] font-semibold px-2.5 py-1 rounded-full"></span>
                        <p id="dd-tanggal" class="text-[11px] text-cream-500"></p>
                    </div>
                    <h3 id="dd-judul" class="mt-2 text-base font-semibold text-cream-900"></h3>
                </div>
                <button onclick="closeDetailModal()"
                    class="text-cream-400 hover:text-cream-700 transition-colors duration-150 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 overflow-y-auto">
                <p id="dd-deskripsi" class="text-sm text-cream-700 leading-relaxed whitespace-pre-line"></p>
            </div>
            <div class="flex items-center justify-end px-6 py-4 border-t border-cream-200">
                <button onclick="closeDetailModal()"
                    class="bg-sage-700 text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-sage-800 transition-colors duration-150">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const pengumumanData = @json($pengumuman->keyBy('id'));

        const typeMeta = {
            penting: {
                badge: 'bg-terracotta-100 text-terracotta-600',
                label: 'Penting'
            },
            informasi: {
                badge: 'bg-sage-100 text-sage-700',
                label: 'Informasi'
            },
            umum: {
                badge: 'bg-cream-100 text-cream-600',
                label: 'Umum'
            },
        };

        function openCreateModal() {
            document.getElementById('modal-create').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('modal-create').classList.add('hidden');
            document.getElementById('form-create').reset();
            document.getElementById('create-error').classList.add('hidden');
        }

        async function submitCreate() {
            const form = document.getElementById('form-create');
            const errorEl = document.getElementById('create-error');
            errorEl.classList.add('hidden');

            const formData = new FormData(form);
            const payload = {
                judul: formData.get('judul'),
                type: formData.get('type'),
                deskripsi: formData.get('deskripsi'),
                tanggal_publish: new Date().toISOString().slice(0, 10),
            };

            if (!payload.judul || !payload.deskripsi) {
                errorEl.textContent = 'Judul dan isi pengumuman wajib diisi.';
                errorEl.classList.remove('hidden');
                return;
            }

            try {
                const res = await fetch('{{ route('admin.pengumuman.store') }}', {
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
                    errorEl.textContent = json.message || 'Gagal membuat pengumuman.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                window.location.reload();
            } catch (err) {
                errorEl.textContent = 'Terjadi kesalahan jaringan.';
                errorEl.classList.remove('hidden');
            }
        }

        function openDetailModal(id) {
            const p = pengumumanData[id];
            const meta = typeMeta[p.type];

            const badgeEl = document.getElementById('dd-badge');
            badgeEl.className = 'text-[10px] font-semibold px-2.5 py-1 rounded-full ' + meta.badge;
            badgeEl.textContent = meta.label;

            document.getElementById('dd-tanggal').textContent = new Date(p.tanggal_publish).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            document.getElementById('dd-judul').textContent = p.judul;
            document.getElementById('dd-deskripsi').textContent = p.deskripsi;

            document.getElementById('modal-detail').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('modal-detail').classList.add('hidden');
        }
    </script>
@endpush
