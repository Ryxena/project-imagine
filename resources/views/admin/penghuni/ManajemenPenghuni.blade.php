@extends('layout.admin.AdminDashboardLayout')

@section('page-title', 'Penghuni')
@section('page-subtitle', 'Kelola data penghuni dan penempatan kamar.')

@section('content')
    @php
        $aktif = $penghunians->whereNull('tanggal_checkout')->values();
        $riwayat = $penghunians->whereNotNull('tanggal_checkout')->values();
    @endphp

    <div class="flex items-center justify-between gap-4 animate-in">
        <div class="relative flex-1 max-w-sm">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-cream-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </span>
            <input type="text" id="search-penghuni" placeholder="Cari nama atau kamar..."
                class="w-full text-sm pl-10 pr-4 py-2.5 rounded-full border border-cream-300 bg-white focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
        </div>

        <button onclick="openModal()"
            class="shrink-0 flex items-center gap-2 bg-sage-700 text-white text-xs font-medium px-4 py-2.5 rounded-md hover:bg-sage-800 hover:shadow-md active:scale-[0.98] transition-all duration-150 ease-out">
            @include('partial.icons.users', ['class' => 'w-3.5 h-3.5'])
            Tambah Penghuni
        </button>
    </div>

    <div class="mt-5 flex items-center gap-6 border-b border-cream-200 animate-in" style="animation-delay: 0.05s">
        <button data-tab="aktif" onclick="switchTab('aktif')"
            class="tab-btn pb-3 text-sm font-semibold text-sage-700 border-b-2 border-sage-700">
            Aktif ({{ $aktif->count() }})
        </button>
        <button data-tab="riwayat" onclick="switchTab('riwayat')"
            class="tab-btn pb-3 text-sm font-medium text-cream-600 border-b-2 border-transparent">
            Riwayat ({{ $riwayat->count() }})
        </button>
    </div>

    <div id="panel-aktif" class="mt-5 animate-in" style="animation-delay: 0.1s">
        <div id="penghuni-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($aktif as $p)
                @php
                    $ditempatkan = !is_null($p->kamar_id);
                @endphp
                <div class="penghuni-card aktif-item bg-white rounded-xl p-4 shadow-sm border border-cream-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-out"
                    data-name="{{ strtolower($p->name) }}" data-kamar="{{ strtolower($p->nomor_kamar ?? '') }}"
                    data-id="{{ $p->id }}" data-full-name="{{ addslashes($p->name) }}"
                    data-email="{{ $p->email }}" data-no-hp="{{ $p->no_hp }}" data-status="{{ $p->status }}"
                    data-nomor-kamar="{{ $p->nomor_kamar ?? '' }}" data-tipe-kamar="{{ $p->tipe_kamar ?? '' }}"
                    data-harga="{{ $p->harga ?? '' }}" data-tanggal-masuk="{{ $p->tanggal_masuk ?? '' }}"
                    data-kamar-id="{{ $p->kamar_id ?? '' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="w-10 h-10 rounded-full bg-sage-200 text-sage-800 text-sm font-semibold flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($p->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-cream-900 truncate">{{ $p->name }}</p>
                                <p class="text-[11px] text-cream-600">
                                    @if ($ditempatkan)
                                        Kamar {{ $p->nomor_kamar }}
                                    @else
                                        Belum ditempatkan
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if ($ditempatkan)
                            <span
                                class="shrink-0 bg-sage-100 text-sage-700 text-[10px] font-semibold px-2.5 py-1 rounded-full">Aktif</span>
                        @else
                            <span
                                class="shrink-0 bg-amber-100 text-amber-600 text-[10px] font-semibold px-2.5 py-1 rounded-full">Belum
                                Ditempatkan</span>
                        @endif
                    </div>

                    <div class="mt-3">
                        @if ($ditempatkan)
                            <button onclick="openDetailModal(this.closest('.penghuni-card'))"
                                class="w-full text-xs font-medium text-cream-700 border border-cream-300 rounded-lg py-2 hover:bg-cream-100 transition-colors duration-150">
                                Lihat Detail
                            </button>
                        @else
                            <button onclick="openAssignModal({{ $p->id }}, '{{ addslashes($p->name) }}')"
                                class="w-full flex items-center justify-center gap-2 bg-sage-700 text-white text-xs font-medium py-2.5 rounded-lg hover:bg-sage-800 active:scale-[0.98] transition-all duration-150 ease-out">
                                Assign ke Kamar
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl border border-cream-200 px-4 py-8 text-center shadow-sm">
                    <p class="text-sm text-cream-600">Belum ada data penghuni.</p>
                </div>
            @endforelse
        </div>

        @if ($aktif->count() > 6)
            <div id="aktif-pagination" class="flex items-center justify-between pt-4">
                <p id="aktif-page-info" class="text-xs text-cream-600"></p>
                <div class="flex gap-2">
                    <button onclick="changePage('aktif', -1)" id="aktif-btn-prev"
                        class="text-xs font-medium text-cream-700 border border-cream-300 rounded-full px-3.5 py-1.5 hover:bg-cream-100 transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                        Sebelumnya
                    </button>
                    <button onclick="changePage('aktif', 1)" id="aktif-btn-next"
                        class="text-xs font-medium text-cream-700 border border-cream-300 rounded-full px-3.5 py-1.5 hover:bg-cream-100 transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                        Berikutnya
                    </button>
                </div>
            </div>
        @endif
    </div>

    <div id="panel-riwayat" class="hidden mt-5 animate-in" style="animation-delay: 0.1s">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($riwayat as $p)
                <div class="riwayat-item bg-white rounded-xl p-4 shadow-sm border border-cream-200 opacity-80"
                    data-name="{{ strtolower($p->name) }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="w-10 h-10 rounded-full bg-cream-200 text-cream-600 text-sm font-semibold flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($p->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-cream-900 truncate">{{ $p->name }}</p>
                                <p class="text-[11px] text-cream-600">Terakhir di Kamar {{ $p->nomor_kamar ?? '-' }}</p>
                            </div>
                        </div>
                        <span
                            class="shrink-0 bg-cream-100 text-cream-600 text-[10px] font-semibold px-2.5 py-1 rounded-full">Checkout</span>
                    </div>
                    <p class="mt-3 text-[11px] text-cream-600">
                        Keluar pada {{ \Carbon\Carbon::parse($p->tanggal_checkout)->translatedFormat('d M Y') }}
                    </p>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl border border-cream-200 px-4 py-8 text-center shadow-sm">
                    <p class="text-sm text-cream-600">Belum ada riwayat penghuni checkout.</p>
                </div>
            @endforelse
        </div>

        @if ($riwayat->count() > 6)
            <div id="riwayat-pagination" class="flex items-center justify-between pt-4">
                <p id="riwayat-page-info" class="text-xs text-cream-600"></p>
                <div class="flex gap-2">
                    <button onclick="changePage('riwayat', -1)" id="riwayat-btn-prev"
                        class="text-xs font-medium text-cream-700 border border-cream-300 rounded-full px-3.5 py-1.5 hover:bg-cream-100 transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                        Sebelumnya
                    </button>
                    <button onclick="changePage('riwayat', 1)" id="riwayat-btn-next"
                        class="text-xs font-medium text-cream-700 border border-cream-300 rounded-full px-3.5 py-1.5 hover:bg-cream-100 transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                        Berikutnya
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: Tambah Penghuni --}}
    <div id="modal-tambah-penghuni" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-md animate-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200">
                <h3 class="text-base font-semibold text-cream-900">Tambah Penghuni Baru</h3>
                <button onclick="closeModal()" class="text-cream-400 hover:text-cream-700 transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-tambah-penghuni" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Masukkan Nama lengkap penghuni"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Email</label>
                    <input type="email" name="email" required placeholder="nama@gmail.com"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Nomor HP</label>
                    <input type="text" name="no_hp" required placeholder="e.g., 08XX XXXX XXXX"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Password Sementara</label>
                    <input type="password" name="password" required minlength="8"
                        placeholder="Minimal 8 karakter"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <p id="tambah-error" class="hidden text-xs text-terracotta-600"></p>
            </form>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
                <button onclick="closeModal()"
                    class="text-sm font-medium text-cream-600 hover:text-cream-900 transition-colors duration-150">Batal</button>
                <button onclick="submitTambahPenghuni()"
                    class="bg-sage-700 text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-sage-800 active:scale-[0.98] transition-all duration-150 ease-out">Tambah
                    Penghuni</button>
            </div>
        </div>
    </div>

    {{-- Modal: Detail Penghuni --}}
    <div id="modal-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeDetailModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-md animate-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200">
                <h3 class="text-base font-semibold text-cream-900">Detail Penghuni</h3>
                <button onclick="closeDetailModal()"
                    class="text-cream-400 hover:text-cream-700 transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5">
                <div class="flex items-center gap-3">
                    <div id="detail-avatar"
                        class="w-12 h-12 rounded-full bg-sage-200 text-sage-800 text-base font-semibold flex items-center justify-center shrink-0">
                    </div>
                    <div>
                        <p id="detail-nama" class="text-base font-semibold text-cream-900"></p>
                        <span id="detail-status"
                            class="inline-block mt-1 bg-sage-100 text-sage-700 text-[10px] font-semibold px-2.5 py-0.5 rounded-full"></span>
                    </div>
                </div>

                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-cream-100">
                        <span class="text-cream-500">Kamar</span>
                        <span id="detail-kamar" class="font-medium text-cream-900"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-cream-100">
                        <span class="text-cream-500">Harga Sewa</span>
                        <span id="detail-harga" class="font-medium text-cream-900"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-cream-100">
                        <span class="text-cream-500">Nomor Telepon</span>
                        <span id="detail-hp" class="font-medium text-cream-900"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-cream-100">
                        <span class="text-cream-500">Email</span>
                        <span id="detail-email" class="font-medium text-cream-900"></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-cream-500">Mulai Kost</span>
                        <span id="detail-tanggal" class="font-medium text-cream-900"></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
                <button onclick="triggerCheckoutFromDetail()"
                    class="text-sm font-medium text-terracotta-600 hover:text-terracotta-700 transition-colors duration-150 mr-auto">Checkout</button>
                <button onclick="triggerEditFromDetail()"
                    class="text-sm font-medium text-cream-700 border border-cream-300 rounded-full px-4 py-2 hover:bg-cream-100 transition-colors duration-150">Edit</button>
            </div>
        </div>
    </div>

    {{-- Modal: Edit Penghuni --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeEditModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-md animate-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200">
                <h3 class="text-base font-semibold text-cream-900">Edit Penghuni</h3>
                <button onclick="closeEditModal()"
                    class="text-cream-400 hover:text-cream-700 transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" id="edit-name" required
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Email</label>
                    <input type="email" id="edit-email" required
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Nomor HP</label>
                    <input type="text" id="edit-no-hp" required
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <p id="edit-error" class="hidden text-xs text-terracotta-600"></p>
            </form>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
                <button onclick="closeEditModal()"
                    class="text-sm font-medium text-cream-600 hover:text-cream-900 transition-colors duration-150">Batal</button>
                <button onclick="submitEdit()"
                    class="bg-sage-700 text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-sage-800 active:scale-[0.98] transition-all duration-150 ease-out">Simpan
                    Perubahan</button>
            </div>
        </div>
    </div>

    {{-- Modal: Assign ke Kamar --}}
    <div id="modal-assign" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeAssignModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-md animate-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-cream-200">
                <h3 class="text-base font-semibold text-cream-900">Assign Kamar — <span id="assign-nama"></span></h3>
                <button onclick="closeAssignModal()"
                    class="text-cream-400 hover:text-cream-700 transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-assign" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Kamar Tersedia</label>
                    <select id="assign-kamar-select" required
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                        <option value="">Memuat kamar tersedia...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Tanggal Masuk</label>
                    <input type="date" id="assign-tanggal" required
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <p id="assign-error" class="hidden text-xs text-terracotta-600"></p>
            </form>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
                <button onclick="closeAssignModal()"
                    class="text-sm font-medium text-cream-600 hover:text-cream-900 transition-colors duration-150">Batal</button>
                <button onclick="submitAssign()"
                    class="bg-sage-700 text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-sage-800 active:scale-[0.98] transition-all duration-150 ease-out">Assign</button>
            </div>
        </div>
    </div>

    {{-- Modal: Checkout --}}
    <div id="modal-checkout" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-cream-900/40" onclick="closeCheckoutModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-sm animate-in">
            <div class="px-6 py-5">
                <h3 class="text-base font-semibold text-cream-900">Checkout <span id="checkout-nama"></span>?</h3>
                <p class="mt-2 text-sm text-cream-600">Penghuni akan dikeluarkan dari kamar. Tagihan yang belum lunas tidak
                    akan dihapus.</p>
                <p id="checkout-error" class="hidden mt-2 text-xs text-terracotta-600"></p>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cream-200">
                <button onclick="closeCheckoutModal()"
                    class="text-sm font-medium text-cream-600 hover:text-cream-900 transition-colors duration-150">Batal</button>
                <button onclick="submitCheckout()"
                    class="bg-terracotta-600 text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-terracotta-700 active:scale-[0.98] transition-all duration-150 ease-out">Ya,
                    Checkout</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ===== Tab, Search & Pagination =====

        const PAGE_SIZE = 6;

        const pageState = {
            aktif: 1,
            riwayat: 1
        };

        let currentTab = 'aktif';


        function switchTab(tab) {
            currentTab = tab;

            document.querySelectorAll('.tab-btn').forEach(btn => {
                const active = btn.dataset.tab === tab;

                btn.classList.toggle('text-sage-700', active);
                btn.classList.toggle('border-sage-700', active);

                btn.classList.toggle('text-cream-600', !active);
                btn.classList.toggle('border-transparent', !active);
            });


            document.getElementById('panel-aktif')
                .classList.toggle('hidden', tab !== 'aktif');

            document.getElementById('panel-riwayat')
                .classList.toggle('hidden', tab !== 'riwayat');


            pageState[tab] = 1;

            renderPagination(tab);
        }



        function renderPagination(tab) {

            const selector = tab === 'aktif' ?
                '.aktif-item' :
                '.riwayat-item';


            const items = Array.from(
                document.querySelectorAll(selector)
            ).filter(item => item.dataset.filteredOut !== 'true');


            const totalPages = Math.max(
                1,
                Math.ceil(items.length / PAGE_SIZE)
            );


            if (pageState[tab] > totalPages) {
                pageState[tab] = totalPages;
            }

            document.querySelectorAll(selector).forEach(item => {
                item.style.display = 'none';
            });


            items.forEach((item, index) => {

                const page =
                    Math.floor(index / PAGE_SIZE) + 1;


                if (page === pageState[tab]) {
                    item.style.display = '';
                }

            });


            const infoEl =
                document.getElementById(`${tab}-page-info`);

            const prevBtn =
                document.getElementById(`${tab}-btn-prev`);

            const nextBtn =
                document.getElementById(`${tab}-btn-next`);



            if (infoEl) {
                infoEl.textContent =
                    `Halaman ${pageState[tab]} dari ${totalPages}`;
            }


            if (prevBtn) {
                prevBtn.disabled =
                    pageState[tab] <= 1;
            }


            if (nextBtn) {
                nextBtn.disabled =
                    pageState[tab] >= totalPages;
            }
        }



        function changePage(tab, delta) {

            pageState[tab] += delta;

            renderPagination(tab);
        }



        document
            .getElementById('search-penghuni')
            .addEventListener('input', function(e) {


                const query =
                    e.target.value.toLowerCase().trim();


                const selector =
                    currentTab === 'aktif' ?
                    '.aktif-item' :
                    '.riwayat-item';



                document
                    .querySelectorAll(selector)
                    .forEach(card => {


                        const name =
                            card.dataset.name ?? '';


                        const kamar =
                            card.dataset.kamar ?? '';



                        const match =
                            name.includes(query) ||
                            kamar.includes(query);



                        card.dataset.filteredOut =
                            match ? 'false' : 'true';


                        card.style.display = '';

                    });



                pageState[currentTab] = 1;

                renderPagination(currentTab);

            });



        document.addEventListener(
            'DOMContentLoaded',
            () => {
                document
                    .querySelectorAll('.aktif-item, .riwayat-item')
                    .forEach(item => {

                        item.dataset.filteredOut = 'false';

                    });


                renderPagination('aktif');

            }
        );

        // ===== Tambah Penghuni =====
        function openModal() {
            document.getElementById('modal-tambah-penghuni').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal-tambah-penghuni').classList.add('hidden');
            document.getElementById('form-tambah-penghuni').reset();
            document.getElementById('tambah-error').classList.add('hidden');
        }

        async function submitTambahPenghuni() {
            const form = document.getElementById('form-tambah-penghuni');
            const errorEl = document.getElementById('tambah-error');
            errorEl.classList.add('hidden');

            const formData = new FormData(form);
            const payload = {
                name: formData.get('name'),
                email: formData.get('email'),
                no_hp: formData.get('no_hp'),
                password: formData.get('password'),
            };

            if (!payload.name || !payload.email || !payload.no_hp || !payload.password) {
                errorEl.textContent = 'Semua field wajib diisi.';
                errorEl.classList.remove('hidden');
                return;
            }

            try {
                const res = await fetch('{{ route('admin.penghuni.store') }}', {
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
                    errorEl.textContent = json.message || 'Gagal menambahkan penghuni.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                window.location.reload();
            } catch (err) {
                errorEl.textContent = 'Terjadi kesalahan jaringan.';
                errorEl.classList.remove('hidden');
            }
        }

        // ===== Detail Modal =====
        let currentDetail = null;

        function openDetailModal(cardEl) {
            const d = cardEl.dataset;
            currentDetail = d;

            document.getElementById('detail-avatar').textContent = d.fullName.charAt(0).toUpperCase();
            document.getElementById('detail-nama').textContent = d.fullName;
            document.getElementById('detail-status').textContent = 'Aktif';
            document.getElementById('detail-kamar').textContent = `${d.nomorKamar} · ${d.tipeKamar}`;
            document.getElementById('detail-harga').textContent = d.harga ?
                'Rp' + Number(d.harga).toLocaleString('id-ID') + ' / bulan' :
                '-';
            document.getElementById('detail-hp').textContent = d.noHp;
            document.getElementById('detail-email').textContent = d.email;
            document.getElementById('detail-tanggal').textContent = d.tanggalMasuk ?
                new Date(d.tanggalMasuk).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }) :
                '-';

            document.getElementById('modal-detail').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('modal-detail').classList.add('hidden');
        }

        function triggerCheckoutFromDetail() {
            closeDetailModal();
            openCheckoutModal(currentDetail.id, currentDetail.fullName);
        }

        function triggerEditFromDetail() {
            closeDetailModal();
            document.getElementById('edit-name').value = currentDetail.fullName;
            document.getElementById('edit-email').value = currentDetail.email;
            document.getElementById('edit-no-hp').value = currentDetail.noHp;
            document.getElementById('modal-edit').dataset.id = currentDetail.id;
            document.getElementById('modal-edit').dataset.kamarId = currentDetail.kamarId;
            document.getElementById('modal-edit').dataset.tanggalMasuk = currentDetail.tanggalMasuk;
            document.getElementById('modal-edit').dataset.status = currentDetail.status;
            document.getElementById('modal-edit').classList.remove('hidden');
        }

        // ===== Edit Modal =====
        function closeEditModal() {
            document.getElementById('modal-edit').classList.add('hidden');
            document.getElementById('edit-error').classList.add('hidden');
        }

        async function submitEdit() {
            const modal = document.getElementById('modal-edit');
            const errorEl = document.getElementById('edit-error');
            errorEl.classList.add('hidden');

            const payload = {
                name: document.getElementById('edit-name').value,
                email: document.getElementById('edit-email').value,
                no_hp: document.getElementById('edit-no-hp').value,
                status: modal.dataset.status,
                kamar_id: modal.dataset.kamarId || null,
                tanggal_masuk: modal.dataset.tanggalMasuk,
            };

            if (!payload.name || !payload.email || !payload.no_hp) {
                errorEl.textContent = 'Semua field wajib diisi.';
                errorEl.classList.remove('hidden');
                return;
            }

            try {
                const res = await fetch(`/admin/penghuni/${modal.dataset.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    errorEl.textContent = json.message || 'Gagal menyimpan perubahan.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                window.location.reload();
            } catch (err) {
                errorEl.textContent = 'Terjadi kesalahan jaringan.';
                errorEl.classList.remove('hidden');
            }
        }

        // ===== Assign Modal =====
        let currentAssignId = null;

        async function openAssignModal(penghunianId, nama) {
            currentAssignId = penghunianId;
            document.getElementById('assign-nama').textContent = nama;
            document.getElementById('modal-assign').classList.remove('hidden');

            const select = document.getElementById('assign-kamar-select');
            select.innerHTML = '<option value="">Memuat kamar tersedia...</option>';

            try {
                const res = await fetch('{{ route('admin.penghuni.daftarkamar') }}', {
                    headers: {
                        'Accept': 'application/json'
                    },
                });
                const json = await res.json();

                if (json.data.length === 0) {
                    select.innerHTML = '<option value="">Tidak ada kamar kosong</option>';
                    return;
                }

                select.innerHTML = '<option value="">Pilih kamar</option>' +
                    json.data.map(k => `<option value="${k.id}">${k.nomor_kamar}</option>`).join('');
            } catch (err) {
                select.innerHTML = '<option value="">Gagal memuat kamar</option>';
            }
        }

        function closeAssignModal() {
            document.getElementById('modal-assign').classList.add('hidden');
            document.getElementById('assign-error').classList.add('hidden');
            currentAssignId = null;
        }

        async function submitAssign() {
            const kamarId = document.getElementById('assign-kamar-select').value;
            const tanggal = document.getElementById('assign-tanggal').value;
            const errorEl = document.getElementById('assign-error');
            errorEl.classList.add('hidden');

            if (!kamarId || !tanggal) {
                errorEl.textContent = 'Pilih kamar dan tanggal masuk.';
                errorEl.classList.remove('hidden');
                return;
            }

            try {
                const res = await fetch(`/admin/penghuni/assignkamar/${currentAssignId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        kamar_id: kamarId,
                        tanggal_masuk: tanggal
                    }),
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    errorEl.textContent = json.message || 'Gagal assign kamar.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                window.location.reload();
            } catch (err) {
                errorEl.textContent = 'Terjadi kesalahan jaringan.';
                errorEl.classList.remove('hidden');
            }
        }

        // ===== Checkout Modal =====
        let currentCheckoutId = null;

        function openCheckoutModal(penghunianId, nama) {
            currentCheckoutId = penghunianId;
            document.getElementById('checkout-nama').textContent = nama;
            document.getElementById('modal-checkout').classList.remove('hidden');
        }

        function closeCheckoutModal() {
            document.getElementById('modal-checkout').classList.add('hidden');
            document.getElementById('checkout-error').classList.add('hidden');
            currentCheckoutId = null;
        }

        async function submitCheckout() {
            const errorEl = document.getElementById('checkout-error');
            errorEl.classList.add('hidden');

            try {
                const res = await fetch(`/admin/penghuni/checkoutkamar/${currentCheckoutId}`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    errorEl.textContent = json.message || 'Gagal checkout.';
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
