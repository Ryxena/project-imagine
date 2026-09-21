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
                class="bg-white rounded-xl p-4 shadow-sm border border-cream-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-out flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span
                            class="{{ $badgeClass }} text-[10px] font-semibold px-2.5 py-1 rounded-full">{{ $badgeLabel }}</span>
                        <p class="text-[11px] text-cream-500">
                            {{ \Carbon\Carbon::parse($p->tanggal_publish)->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-cream-900">{{ $p->judul }}</p>
                    <p class="mt-1.5 text-xs text-cream-600 line-clamp-3">{{ $p->deskripsi }}</p>
                </div>

                <div class="mt-4 pt-3 border-t border-cream-100 flex items-center justify-between">
                    <button onclick="openDetailModal({{ $p->id }})"
                        class="text-xs font-medium text-sage-700 hover:text-sage-800 transition-colors duration-150">
                        Lihat Detail
                    </button>
                    <div class="flex items-center gap-1.5">
                        <button onclick="openEditModal({{ $p->id }})" title="Edit Pengumuman"
                            class="w-7 h-7 rounded-lg text-cream-400 hover:text-sage-700 hover:bg-sage-50 flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                        <button onclick="openDeleteModal({{ $p->id }}, '{{ addslashes($p->judul) }}')"
                            title="Hapus Pengumuman"
                            class="w-7 h-7 rounded-lg text-cream-400 hover:text-terracotta-600 hover:bg-terracotta-50 flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
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

    <div id="modal-form" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeFormModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-lg animate-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200">
                <div>
                    <h3 id="form-modal-title" class="text-base font-semibold text-cream-900">Buat Pengumuman Baru</h3>
                    <p class="mt-0.5 text-xs text-cream-600">Informasi akan disebarkan ke seluruh penghuni aktif.</p>
                </div>
                <button onclick="closeFormModal()"
                    class="text-cream-400 hover:text-cream-700 transition-colors duration-150 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="pengumuman-form" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" id="pengumuman-id" name="id">
                <input type="hidden" id="form-method" value="POST">

                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Judul Pengumuman</label>
                    <input type="text" id="input-judul" name="judul" required
                        placeholder="Contoh: Jadwal Pemadaman Listrik"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Kategori</label>
                    <select id="input-type" name="type" required
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                        <option value="umum">Umum</option>
                        <option value="informasi">Informasi</option>
                        <option value="penting">Penting</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Isi Pengumuman</label>
                    <textarea id="input-deskripsi" name="deskripsi" rows="5" required
                        placeholder="Tuliskan detail pengumuman di sini..."
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150 resize-none"></textarea>
                </div>
                <p id="form-error" class="hidden text-xs text-terracotta-600"></p>
            </form>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
                <button onclick="closeFormModal()"
                    class="text-sm font-medium text-cream-600 hover:text-cream-900 transition-colors duration-150">Batal</button>
                <button onclick="submitPengumumanForm()" id="form-submit-btn"
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
                        <p id="dd-tanggal" class="text-[11px] text-cream-500 font-medium"></p>
                    </div>
                    <h3 id="dd-judul" class="mt-2 text-base font-semibold text-cream-900"></h3>
                </div>
                <button onclick="closeDetailModal()"
                    class="text-cream-400 hover:text-cream-700 transition-colors duration-150 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
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

    <div id="modal-delete" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeDeleteModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-sm p-6 text-center animate-in">
            <div
                class="w-12 h-12 rounded-2xl bg-terracotta-100 text-terracotta-600 flex items-center justify-center mx-auto mb-4">
                @include('partial.icons.warning', ['class' => 'w-6 h-6'])
            </div>
            <h3 class="text-base font-semibold text-cream-900">Hapus Pengumuman?</h3>
            <p id="delete-message" class="text-xs text-cream-600 mt-1 leading-relaxed"></p>
            <input type="hidden" id="delete-id">
            <div class="mt-6 flex items-center justify-center gap-3">
                <button onclick="closeDeleteModal()"
                    class="w-full text-xs font-semibold text-cream-700 bg-cream-100 hover:bg-cream-200 py-2.5 rounded-xl transition-colors">Batal</button>
                <button onclick="executeDelete()"
                    class="w-full text-xs font-semibold text-white bg-terracotta-600 hover:bg-terracotta-700 py-2.5 rounded-xl transition-colors">Ya,
                    Hapus</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        if (new URLSearchParams(window.location.search).get('action') === 'create') {
            openCreateModal();
        }

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
            document.getElementById('form-modal-title').textContent = 'Buat Pengumuman Baru';
            document.getElementById('form-submit-btn').textContent = 'Umumkan';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('pengumuman-id').value = '';
            document.getElementById('pengumuman-form').reset();
            document.getElementById('form-error').classList.add('hidden');
            document.getElementById('modal-form').classList.remove('hidden');
        }

        function openEditModal(id) {
            const p = pengumumanData[id];
            document.getElementById('form-modal-title').textContent = 'Edit Pengumuman';
            document.getElementById('form-submit-btn').textContent = 'Simpan Perubahan';
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('pengumuman-id').value = p.id;

            document.getElementById('input-judul').value = p.judul;
            document.getElementById('input-type').value = p.type;
            document.getElementById('input-deskripsi').value = p.deskripsi;

            document.getElementById('form-error').classList.add('hidden');
            document.getElementById('modal-form').classList.remove('hidden');
        }

        function closeFormModal() {
            document.getElementById('modal-form').classList.add('hidden');
        }

        async function submitPengumumanForm() {
            const method = document.getElementById('form-method').value;
            const id = document.getElementById('pengumuman-id').value;
            const errorEl = document.getElementById('form-error');
            errorEl.classList.add('hidden');

            const payload = {
                judul: document.getElementById('input-judul').value,
                type: document.getElementById('input-type').value,
                deskripsi: document.getElementById('input-deskripsi').value,
                tanggal_publish: new Date().toISOString().slice(0, 10),
            };

            if (!payload.judul || !payload.deskripsi) {
                errorEl.textContent = 'Judul dan isi pengumuman wajib diisi.';
                errorEl.classList.remove('hidden');
                return;
            }

            let url = '{{ route('admin.pengumuman.store') }}';
            if (method === 'PUT') {
                url = `/admin/pengumuman/${id}`;
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
                    errorEl.textContent = json.message || 'Gagal menyimpan pengumuman.';
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

            const createdAt = new Date(p.created_at || p.tanggal_publish);
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('dd-tanggal').textContent = createdAt.toLocaleDateString('id-ID', options) + ' WIB';

            document.getElementById('dd-judul').textContent = p.judul;
            document.getElementById('dd-deskripsi').textContent = p.deskripsi;

            document.getElementById('modal-detail').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('modal-detail').classList.add('hidden');
        }

        function openDeleteModal(id, judul) {
            document.getElementById('delete-id').value = id;
            document.getElementById('delete-message').innerHTML =
                `Apakah Anda yakin ingin menghapus pengumuman <strong>"${judul}"</strong>?`;
            document.getElementById('modal-delete').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('modal-delete').classList.add('hidden');
        }

        async function executeDelete() {
            const id = document.getElementById('delete-id').value;
            try {
                const res = await fetch(`/admin/pengumuman/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    alert(json.message || 'Gagal menghapus pengumuman.');
                    return;
                }

                window.location.reload();
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            }
        }
    </script>
@endpush
