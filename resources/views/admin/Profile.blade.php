@extends('layout.admin.AdminDashboardLayout')

@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi data diri dan keamanan akun Anda.')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 animate-in">
        <div class="lg:col-span-4">
            <div class="bg-white rounded-xl shadow-sm border border-cream-200 p-5 text-center">
                <div class="w-16 h-16 mx-auto rounded-xl bg-sage-200 text-sage-800 text-xl font-bold flex items-center justify-center">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h3 class="mt-3 text-sm font-semibold text-cream-900">{{ $user->name }}</h3>
                <span class="inline-block mt-1 bg-sage-100 text-sage-700 text-[10px] font-semibold px-2 py-0.5 rounded-full">Owner</span>

                <div class="mt-4 pt-4 border-t border-cream-100 text-left space-y-2.5">
                    <div>
                        <p class="text-[10px] text-cream-500 uppercase tracking-wide">Email Akun (Read-only)</p>
                        <p class="mt-0.5 text-xs font-medium text-cream-900 truncate">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-cream-500 uppercase tracking-wide">Bergabung Sejak</p>
                        <p class="mt-0.5 text-xs font-medium text-cream-900">{{ $user->created_at->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-cream-200 p-5">
                <h3 class="text-xs font-semibold text-cream-900 pb-3 border-b border-cream-100 uppercase tracking-wider">Informasi Pribadi</h3>

                <form id="form-info" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-medium text-cream-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="edit-name" value="{{ $user->name }}"
                            class="w-full text-xs px-3 py-2 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-cream-700 mb-1">Nomor Telepon</label>
                        <input type="text" id="edit-no-hp" value="{{ $user->no_hp }}"
                            class="w-full text-xs px-3 py-2 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                    </div>
                    <div class="md:col-span-2">
                        <p id="info-error" class="hidden text-xs text-terracotta-600"></p>
                        <p id="info-success" class="hidden text-xs text-sage-700"></p>
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <button type="button" onclick="submitInfo()" class="bg-sage-700 text-white text-xs font-medium px-4 py-2 rounded-full hover:bg-sage-800 active:scale-[0.98] transition-all duration-150 ease-out">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-cream-200 p-5">
                <h3 class="text-xs font-semibold text-cream-900 pb-3 border-b border-cream-100 uppercase tracking-wider">Keamanan Akun</h3>

                <form id="form-password" class="mt-4 space-y-3.5 max-w-md">
                    <div>
                        <label class="block text-xs font-medium text-cream-700 mb-1">Password Baru</label>
                        <input type="password" id="new-password" placeholder="Masukkan password baru"
                            class="w-full text-xs px-3 py-2 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                        <p class="mt-0.5 text-[10px] text-cream-500">Minimal 8 karakter.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-cream-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" id="confirm-password" placeholder="Ulangi password baru"
                            class="w-full text-xs px-3 py-2 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                    </div>
                    <p id="password-error" class="hidden text-xs text-terracotta-600"></p>
                    <p id="password-success" class="hidden text-xs text-sage-700"></p>
                    <div>
                        <button type="button" onclick="submitPassword()" class="text-sage-700 border border-sage-300 text-xs font-medium px-4 py-2 rounded-full hover:bg-sage-50 active:scale-[0.98] transition-all duration-150 ease-out">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
async function submitInfo() {
    const errorEl = document.getElementById('info-error');
    const successEl = document.getElementById('info-success');
    errorEl.classList.add('hidden');
    successEl.classList.add('hidden');

    const name = document.getElementById('edit-name').value.trim();
    const noHp = document.getElementById('edit-no-hp').value.trim();

    if (!name || !noHp) {
        errorEl.textContent = 'Nama dan nomor telepon wajib diisi.';
        errorEl.classList.remove('hidden');
        return;
    }

    try {
        const res = await fetch('{{ route("admin.profile.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ name: name, no_hp: noHp }),
        });
        const json = await res.json();

        if (!res.ok) {
            errorEl.textContent = json.message || 'Gagal menyimpan perubahan.';
            errorEl.classList.remove('hidden');
            return;
        }

        successEl.textContent = 'Perubahan berhasil disimpan.';
        successEl.classList.remove('hidden');
        setTimeout(() => window.location.reload(), 1000);
    } catch (err) {
        errorEl.textContent = 'Terjadi kesalahan jaringan.';
        errorEl.classList.add('hidden');
    }
}

async function submitPassword() {
    const errorEl = document.getElementById('password-error');
    const successEl = document.getElementById('password-success');
    errorEl.classList.add('hidden');
    successEl.classList.add('hidden');

    const password = document.getElementById('new-password').value;
    const confirm = document.getElementById('confirm-password').value;

    if (!password || password.length < 8) {
        errorEl.textContent = 'Password baru minimal 8 karakter.';
        errorEl.classList.remove('hidden');
        return;
    }
    if (password !== confirm) {
        errorEl.textContent = 'Konfirmasi password tidak cocok.';
        errorEl.classList.remove('hidden');
        return;
    }

    try {
        const res = await fetch('{{ route("admin.profile.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                password: password,
                password_confirmation: confirm,
            }),
        });
        const json = await res.json();

        if (!res.ok) {
            errorEl.textContent = json.message || 'Gagal mengganti password.';
            errorEl.classList.remove('hidden');
            return;
        }

        successEl.textContent = 'Password berhasil diganti.';
        successEl.classList.remove('hidden');
        document.getElementById('form-password').reset();
    } catch (err) {
        errorEl.textContent = 'Terjadi kesalahan jaringan.';
        errorEl.classList.add('hidden');
    }
}
</script>
@endpush